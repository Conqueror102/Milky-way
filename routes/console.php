<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:make-admin {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if ($user === null) {
        $this->error("No user found with email {$email}.");

        return 1;
    }

    $user->forceFill(['is_admin' => true])->save();

    $this->info("{$user->email} can now manage the store.");

    return 0;
})->purpose('Give a registered user access to the store admin');
