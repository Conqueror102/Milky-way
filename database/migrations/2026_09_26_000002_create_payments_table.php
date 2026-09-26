<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per payment attempt, so the admin can see every try (paid, failed or
     * abandoned), not just the latest one that the order itself remembers.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('reference')->unique();
            $table->string('status')->default('pending')->index();
            // Whole naira: what was asked for, and what the provider says actually came in.
            $table->unsignedInteger('amount');
            $table->unsignedInteger('amount_paid')->nullable();
            $table->string('message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // Orders that already went to a provider keep their latest attempt.
        DB::table('orders')->whereNotNull('payment_reference')->orderBy('id')->each(function (object $order) {
            DB::table('payments')->insert([
                'order_id' => $order->id,
                'provider' => $order->payment_provider ?? 'paystack',
                'reference' => $order->payment_reference,
                'status' => match ($order->payment_status) {
                    'paid' => 'successful',
                    'failed' => 'failed',
                    default => 'pending',
                },
                'amount' => $order->subtotal,
                'amount_paid' => $order->payment_status === 'paid' ? $order->subtotal : null,
                'paid_at' => $order->paid_at,
                'created_at' => $order->updated_at,
                'updated_at' => $order->updated_at,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
