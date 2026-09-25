<?php

use App\Models\User;

test('guests are redirected to login', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('login'));
});

test('customers cannot reach the admin', function (string $route) {
    $this->actingAs(User::factory()->create());

    $this->get(route($route))->assertForbidden();
})->with(['admin.products.index', 'admin.products.create', 'admin.orders.index']);

test('admins can reach the admin', function (string $route) {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route($route))->assertOk();
})->with(['admin.products.index', 'admin.products.create', 'admin.orders.index']);

test('only admins see the store links in the sidebar', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('dashboard'))->assertDontSee(route('admin.products.index'));

    $this->actingAs(User::factory()->admin()->create());
    $this->get(route('dashboard'))->assertSee(route('admin.products.index'));
});

test('the make-admin command promotes a user', function () {
    $user = User::factory()->create(['email' => 'owner@example.com']);

    $this->artisan('app:make-admin', ['email' => 'owner@example.com'])->assertSuccessful();

    expect($user->refresh()->is_admin)->toBeTrue();
});

test('the make-admin command fails for an unknown email', function () {
    $this->artisan('app:make-admin', ['email' => 'nobody@example.com'])->assertFailed();
});
