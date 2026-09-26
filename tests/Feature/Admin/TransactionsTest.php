<?php

use App\Livewire\Admin\Transactions\Index;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->admin()->create());
});

test('admins see every payment attempt with its order and customer', function () {
    $order = Order::factory()->create(['customer_name' => 'Ada Obi', 'subtotal' => 12500]);
    Payment::factory()->for($order)->create(['reference' => 'TRY-ONE', 'status' => 'failed', 'message' => 'Declined', 'amount' => 12500]);
    Payment::factory()->for($order)->successful()->create(['reference' => 'TRY-TWO', 'amount' => 12500]);

    $this->get(route('admin.transactions.index'))
        ->assertOk()
        ->assertSee('TRY-ONE')
        ->assertSee('TRY-TWO')
        ->assertSee('Ada Obi')
        ->assertSee('Declined')
        ->assertSee(route('admin.orders.show', $order));
});

test('transactions can be filtered by status, searched and limited by date', function () {
    $ada = Order::factory()->create(['customer_name' => 'Ada Obi']);
    $bola = Order::factory()->create(['customer_name' => 'Bola Ade', 'customer_email' => 'bola@example.com']);
    Payment::factory()->for($ada)->successful()->create(['reference' => 'REF-ADA', 'amount' => 5000]);
    Payment::factory()->for($bola)->create(['reference' => 'REF-BOLA', 'status' => 'failed']);
    Payment::factory()->for($bola)->create(['reference' => 'REF-OLD', 'created_at' => now()->subMonths(2)]);

    Livewire::test(Index::class)
        ->set('status', 'successful')
        ->assertSee('REF-ADA')
        ->assertDontSee('REF-BOLA')
        ->set('status', '')
        ->set('search', 'bola@example')
        ->assertSee('REF-BOLA')
        ->assertSee('REF-OLD')
        ->assertDontSee('REF-ADA')
        ->set('from', now()->subWeek()->toDateString())
        ->assertSee('REF-BOLA')
        ->assertDontSee('REF-OLD')
        ->call('clearFilters')
        ->assertSee('REF-ADA')
        ->assertSee('REF-OLD');
});

test('the summary totals the money received for the matching transactions', function () {
    Payment::factory()->successful()->create(['amount' => 5000]);
    Payment::factory()->successful()->create(['amount' => 7000]);
    Payment::factory()->create(['status' => 'failed', 'amount' => 9000]);

    expect(Livewire::test(Index::class)->instance()->summary)
        ->toBe(['count' => 3, 'successful' => 2, 'received' => 12000, 'failed' => 1]);
});

test('an order page lists its payment attempts', function () {
    $order = Order::factory()->create();
    Payment::factory()->for($order)->create(['reference' => 'ATTEMPT-1', 'status' => 'abandoned']);

    $this->get(route('admin.orders.show', $order))->assertOk()->assertSee('ATTEMPT-1')->assertSee('Abandoned');
});
