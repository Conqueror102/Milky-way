<?php

use App\Services\Cloudinary;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.cloudinary.url' => 'cloudinary://key123:secret456@demo-cloud']);
});

test('it reads credentials from CLOUDINARY_URL', function () {
    expect(Cloudinary::fromConfig()->isConfigured())->toBeTrue();
});

test('it is not configured without credentials', function () {
    config(['services.cloudinary.url' => null]);

    expect(Cloudinary::fromConfig()->isConfigured())->toBeFalse();
});

test('uploads are signed and return the secure url', function () {
    $this->freezeTime();

    Http::fake([
        'api.cloudinary.com/v1_1/demo-cloud/image/upload' => Http::response([
            'secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/v1/milky-way/products/abc.jpg',
            'public_id' => 'milky-way/products/abc',
        ]),
    ]);

    $result = Cloudinary::fromConfig()->upload(UploadedFile::fake()->image('cream.jpg'));

    expect($result)->toBe([
        'url' => 'https://res.cloudinary.com/demo-cloud/image/upload/v1/milky-way/products/abc.jpg',
        'public_id' => 'milky-way/products/abc',
    ]);

    $timestamp = now()->getTimestamp();

    Http::assertSent(function (Request $request) use ($timestamp) {
        $fields = collect($request->data())->pluck('contents', 'name');

        return $fields['api_key'] === 'key123'
            && $fields['folder'] === 'milky-way/products'
            && $fields['signature'] === sha1("folder=milky-way/products&timestamp={$timestamp}secret456");
    });
});

test('a failed upload throws', function () {
    Http::fake(['api.cloudinary.com/*' => Http::response(['error' => ['message' => 'Invalid Signature']], 401)]);

    Cloudinary::fromConfig()->upload(UploadedFile::fake()->image('cream.jpg'));
})->throws(RuntimeException::class, 'Invalid Signature');

test('images can be destroyed', function () {
    Http::fake(['api.cloudinary.com/v1_1/demo-cloud/image/destroy' => Http::response(['result' => 'ok'])]);

    expect(Cloudinary::fromConfig()->destroy('milky-way/products/abc'))->toBeTrue();

    Http::assertSent(fn (Request $request) => $request['public_id'] === 'milky-way/products/abc');
});
