<?php

use App\Livewire\Admin\Delivery;
use App\Models\DeliveryArea;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    DeliveryArea::query()->delete();
});

function deliveryPlaces(): array
{
    return DeliveryArea::query()->ordered()->get()
        ->map(fn (DeliveryArea $area) => [$area->name, $area->fee, $area->is_featured])
        ->all();
}

test('customers cannot reach the delivery page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.delivery'))->assertForbidden();
});

test('admins see every place with its fee', function () {
    $this->actingAs(User::factory()->admin()->create());
    DeliveryArea::factory()->create(['name' => 'Kano', 'fee' => 6000]);

    Livewire::test(Delivery::class)
        ->assertSet('places.0.name', 'Kano')
        ->assertSet('places.0.fee', '6000');

    $this->get(route('admin.delivery'))->assertOk()->assertSee('Add a place');
});

test('admins add places with a fee, free delivery, or a fee to arrange later', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Delivery::class)
        ->call('addPlace')
        ->set('places.0', ['id' => null, 'name' => ' Lagos ', 'fee' => '2500', 'featured' => true])
        ->call('addPlace')
        ->set('places.1', ['id' => null, 'name' => 'Ikeja', 'fee' => '0', 'featured' => false])
        ->call('addPlace')
        ->set('places.2', ['id' => null, 'name' => 'London, UK', 'fee' => '', 'featured' => false])
        ->call('save')
        ->assertHasNoErrors();

    expect(deliveryPlaces())->toBe([
        ['Lagos', 2500, true],
        ['Ikeja', 0, false],
        ['London, UK', null, false],
    ]);
});

test('admins rename, reorder and remove places', function () {
    $this->actingAs(User::factory()->admin()->create());
    DeliveryArea::factory()->create(['name' => 'Lagos', 'sort_order' => 0]);
    DeliveryArea::factory()->create(['name' => 'Ogun', 'sort_order' => 1]);
    DeliveryArea::factory()->create(['name' => 'Accra', 'sort_order' => 2]);

    Livewire::test(Delivery::class)
        ->set('places.2.name', 'Accra, Ghana')
        ->call('movePlace', 2, -1)
        ->call('removePlace', 0)
        ->call('save')
        ->assertHasNoErrors();

    expect(deliveryPlaces())->toBe([
        ['Accra, Ghana', null, false],
        ['Ogun', null, false],
    ]);
});

test('a place needs a name that is not already on the list', function () {
    $this->actingAs(User::factory()->admin()->create());
    DeliveryArea::factory()->create(['name' => 'Lagos']);

    Livewire::test(Delivery::class)
        ->call('addPlace')
        ->set('places.1.name', 'lagos')
        ->call('addPlace')
        ->call('save')
        ->assertHasErrors(['places.1.name' => 'distinct', 'places.2.name' => 'required'])
        ->assertSee('This place is already on the list.')
        ->assertSee('Enter the name of the place.');

    expect(DeliveryArea::count())->toBe(1);
});

test('a fee must be a whole number of naira, not below zero', function () {
    $this->actingAs(User::factory()->admin()->create());
    DeliveryArea::factory()->create(['name' => 'Lagos']);
    DeliveryArea::factory()->create(['name' => 'Ogun']);

    Livewire::test(Delivery::class)
        ->set('places.0.fee', '-100')
        ->set('places.1.fee', '12.50')
        ->call('save')
        ->assertHasErrors(['places.0.fee' => 'min', 'places.1.fee' => 'integer'])
        ->assertSee('The fee field must be at least 0.');

    expect(DeliveryArea::whereNotNull('fee')->count())->toBe(0);
});

test('at most four places can be shown on the home page', function () {
    $this->actingAs(User::factory()->admin()->create());
    DeliveryArea::factory()->featured()->count(5)->create();

    Livewire::test(Delivery::class)
        ->call('save')
        ->assertHasErrors('places')
        ->assertSee('Show at most 4 places on the home page.');
});
