<?php

use App\Enums\OrderStatus;
use App\Livewire\Admin\Orders\Index;
use App\Livewire\Admin\Orders\Show;
use App\Models\Order;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

test('admins see orders and can filter by status', function () {
    $pending = Order::factory()->create(['customer_name' => 'Ada Pending', 'status' => 'pending']);
    $shipped = Order::factory()->create(['customer_name' => 'Bola Dispatched', 'status' => 'dispatched']);

    Livewire::test(Index::class)
        ->assertSee('Ada Pending')
        ->assertSee('Bola Dispatched')
        ->set('status', 'dispatched')
        ->assertDontSee('Ada Pending')
        ->assertSee('Bola Dispatched');
});

test('admins can view an order and change its status', function () {
    $order = Order::factory()->create(['status' => 'pending']);

    $this->get(route('admin.orders.show', $order))->assertOk()->assertSee($order->customer_name)->assertSee($order->reference);

    Livewire::test(Show::class, ['order' => $order])
        ->set('status', 'dispatched')
        ->call('updateStatus')
        ->assertHasNoErrors();

    expect($order->refresh()->status)->toBe(OrderStatus::Dispatched);
});

test('order status must be a known value', function () {
    $order = Order::factory()->create();

    Livewire::test(Show::class, ['order' => $order])
        ->set('status', 'teleported')
        ->call('updateStatus')
        ->assertHasErrors('status');
});
