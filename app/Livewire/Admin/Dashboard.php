<?php

namespace App\Livewire\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use stdClass;

#[Title('Dashboard')]
class Dashboard extends Component
{
    /** Products at or below this many left show up as running low. */
    public const LOW_STOCK = 5;

    /**
     * Money in and paid orders for today, this week and this month.
     *
     * @return array<string, array{label: string, revenue: int, orders: int}>
     */
    #[Computed]
    public function sales(): array
    {
        $periods = [
            'today' => [__('Today'), now()->startOfDay()],
            'week' => [__('This week'), now()->startOfWeek()],
            'month' => [__('This month'), now()->startOfMonth()],
        ];

        return collect($periods)->map(function (array $period) {
            $paid = $this->paidOrdersSince($period[1]);

            return [
                'label' => $period[0],
                'revenue' => (int) $paid->sum('subtotal'),
                'orders' => $paid->count(),
            ];
        })->all();
    }

    #[Computed]
    public function averageOrderValue(): int
    {
        return (int) round($this->paidOrdersSince(now()->subDays(30))->avg('subtotal') ?? 0);
    }

    /**
     * How many orders sit in each status.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function ordersByStatus(): array
    {
        $counts = Order::query()->toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status) => [$status->value => (int) ($counts[$status->value] ?? 0)])
            ->all();
    }

    /** Orders placed but not yet paid for (and not cancelled). */
    #[Computed]
    public function awaitingPayment(): int
    {
        return Order::query()
            ->where('payment_status', '!=', PaymentStatus::Paid)
            ->where('status', '!=', OrderStatus::Cancelled)
            ->count();
    }

    #[Computed]
    public function failedPaymentsThisWeek(): int
    {
        return Payment::query()
            ->where('status', TransactionStatus::Failed)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    /**
     * Money in for each of the last 30 days, oldest first.
     *
     * @return SupportCollection<int, array{date: CarbonImmutable, revenue: int, orders: int}>
     */
    #[Computed]
    public function dailySales(): SupportCollection
    {
        $start = now()->subDays(29)->startOfDay();
        $byDay = $this->paidOrdersSince($start)->groupBy(fn (Order $order): string => $order->paid_at?->toDateString() ?? '');

        return collect(range(0, 29))->map(function (int $offset) use ($start, $byDay) {
            $date = $start->copy()->addDays($offset);
            $orders = $byDay->get($date->toDateString(), collect());

            return ['date' => $date, 'revenue' => (int) $orders->sum('subtotal'), 'orders' => $orders->count()];
        });
    }

    /**
     * @return Collection<int, Order>
     */
    #[Computed]
    public function recentOrders(): Collection
    {
        return Order::query()->latest()->latest('id')->limit(8)->get();
    }

    /**
     * Products that are sold out or nearly so, emptiest first. Products without a
     * stock count are left out: nobody is counting them.
     *
     * @return Collection<int, Product>
     */
    #[Computed]
    public function lowStock(): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->whereNotNull('stock')
            ->where('stock', '<=', self::LOW_STOCK)
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(8)
            ->get();
    }

    /**
     * What sold most in the last 30 days, by units, counting paid orders only. Each row
     * has product_name, product_id, units and revenue.
     *
     * @return SupportCollection<int, stdClass>
     */
    #[Computed]
    public function bestSellers(): SupportCollection
    {
        return OrderItem::query()
            ->toBase()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', PaymentStatus::Paid->value)
            ->where('orders.paid_at', '>=', now()->subDays(30))
            ->selectRaw('order_items.product_name, max(order_items.product_id) as product_id, sum(order_items.quantity) as units, sum(order_items.line_total) as revenue')
            ->groupBy('order_items.product_name')
            ->orderByDesc('units')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
    }

    /**
     * @return Collection<int, Order>
     */
    private function paidOrdersSince(CarbonInterface $since): Collection
    {
        return Order::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->where('paid_at', '>=', $since)
            ->get(['id', 'subtotal', 'paid_at']);
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard');
    }
}
