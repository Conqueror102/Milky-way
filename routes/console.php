<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:make-admin {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    // There is no sign-up page, so a new admin's account is made here.
    if ($user === null) {
        $name = (string) $this->ask('No account uses that email yet. What name should the new account have?');
        $password = (string) $this->secret('Choose a password (at least 8 characters)');

        if (blank($name) || strlen($password) < 8) {
            $this->error('A name and a password of at least 8 characters are needed.');

            return 1;
        }

        $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        $user->forceFill(['email_verified_at' => now()])->save();
    }

    $user->forceFill(['is_admin' => true])->save();

    $this->info("{$user->email} can now manage the store.");

    return 0;
})->purpose('Give an account access to the store admin, making the account if needed');
