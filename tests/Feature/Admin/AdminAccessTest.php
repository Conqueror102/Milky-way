<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
    $this->get(route('profile.edit'))->assertDontSee(route('admin.products.index'));

    $this->actingAs(User::factory()->admin()->create());
    $this->get(route('profile.edit'))->assertSee(route('admin.products.index'));
});

test('the make-admin command promotes a user', function () {
    $user = User::factory()->create(['email' => 'owner@example.com']);

    $this->artisan('app:make-admin', ['email' => 'owner@example.com'])->assertSuccessful();

    expect($user->refresh()->is_admin)->toBeTrue();
});

test('the make-admin command makes an admin account for a new email', function () {
    $this->artisan('app:make-admin', ['email' => 'new@example.com'])
        ->expectsQuestion('No account uses that email yet. What name should the new account have?', 'Ada Obi')
        ->expectsQuestion('Choose a password (at least 8 characters)', 'correct-horse')
        ->assertSuccessful();

    $user = User::where('email', 'new@example.com')->sole();
    expect($user->name)->toBe('Ada Obi')
        ->and($user->is_admin)->toBeTrue()
        ->and($user->hasVerifiedEmail())->toBeTrue()
        ->and(Hash::check('correct-horse', $user->password))->toBeTrue();
});

test('the make-admin command refuses a short password for a new account', function () {
    $this->artisan('app:make-admin', ['email' => 'new@example.com'])
        ->expectsQuestion('No account uses that email yet. What name should the new account have?', 'Ada Obi')
        ->expectsQuestion('Choose a password (at least 8 characters)', 'short')
        ->assertFailed();

    expect(User::where('email', 'new@example.com')->exists())->toBeFalse();
});

test('accounts listed in ADMIN_EMAILS can open the admin', function () {
    config(['auth.admin_emails' => ['owner@example.com', 'second@example.com']]);

    $this->actingAs(User::factory()->create(['email' => 'Owner@Example.com']))
        ->get(route('admin.products.index'))
        ->assertOk();

    $this->actingAs(User::factory()->create(['email' => 'someone@example.com']))
        ->get(route('admin.products.index'))
        ->assertForbidden();
});
