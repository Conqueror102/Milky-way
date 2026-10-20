<?php

namespace App\Console\Commands;

use App\Actions\Shop\CancelOrder;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

#[Signature('app:expire-unpaid-orders')]
#[Description('Cancel pending unpaid orders after 24 hours and release their stock')]
class ExpireUnpaidOrders extends Command
{
    public function handle(CancelOrder $cancelOrder): int
    {
        $count = 0;

        Order::query()
            ->where('status', OrderStatus::Pending)
            ->where('payment_status', '!=', PaymentStatus::Paid)
            ->where('created_at', '<=', now()->subDay())
            ->chunkById(100, function (Collection $orders) use ($cancelOrder, &$count): void {
                foreach ($orders as $order) {
                    if ($cancelOrder->handle($order, expiredOnly: true)) {
                        $count++;
                    }
                }
            });

        $this->info("Cancelled {$count} expired unpaid orders.");

        return self::SUCCESS;
    }
}
