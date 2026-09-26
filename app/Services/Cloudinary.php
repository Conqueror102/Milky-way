<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Minimal client for Cloudinary's signed upload API.
 *
 * @see https://cloudinary.com/documentation/image_upload_api_reference
 */
class Cloudinary
{
    public function __construct(
        protected ?string $cloudName,
        protected ?string $apiKey,
        protected ?string $apiSecret,
        protected string $folder,
    ) {}

    /**
     * Build the client from config/services.php, preferring CLOUDINARY_URL.
     */
    public static function fromConfig(): self
    {
        /** @var array{url: ?string, cloud_name: ?string, api_key: ?string, api_secret: ?string, folder: string} $config */
        $config = config('services.cloudinary');

        if (filled($config['url'])) {
            $parts = parse_url($config['url']);

            return new self(
                $parts['host'] ?? null,
                isset($parts['user']) ? urldecode($parts['user']) : null,
                isset($parts['pass']) ? urldecode($parts['pass']) : null,
                $config['folder'],
            );
        }

        return new self($config['cloud_name'], $config['api_key'], $config['api_secret'], $config['folder']);
    }

    public function isConfigured(): bool
    {
        return filled($this->cloudName) && filled($this->apiKey) && filled($this->apiSecret);
    }

    /**
     * Upload an image and return its secure URL and public id. It goes in the
     * configured folder unless another is given.
     *
     * @return array{url: string, public_id: string}
     */
    public function upload(UploadedFile $file, ?string $folder = null): array
    {
        $response = Http::asMultipart()
            ->attach('file', (string) file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post($this->endpoint('upload'), $this->signed(['folder' => $folder ?? $this->folder]));

        if ($response->failed()) {
            throw new RuntimeException('Cloudinary upload failed: '.$response->json('error.message', $response->body()));
        }

        return [
            'url' => (string) $response->json('secure_url'),
            'public_id' => (string) $response->json('public_id'),
        ];
    }

    /**
     * Delete an uploaded image. Failures are reported to the caller as false.
     */
    public function destroy(string $publicId): bool
    {
        $response = Http::asForm()->post($this->endpoint('destroy'), $this->signed(['public_id' => $publicId]));

        return $response->successful() && in_array($response->json('result'), ['ok', 'not found'], true);
    }

    protected function endpoint(string $action): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Cloudinary is not configured. Set CLOUDINARY_URL in the environment.');
        }

        return "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/{$action}";
    }

    /**
     * Add the api key, timestamp and signature Cloudinary requires.
     *
     * @param  array<string, string>  $params
     * @return array<string, string>
     */
    protected function signed(array $params): array
    {
        $params['timestamp'] = (string) now()->getTimestamp();

        ksort($params);

        $toSign = collect($params)->map(fn (string $value, string $key) => "{$key}={$value}")->implode('&');

        return [
            ...$params,
            'api_key' => (string) $this->apiKey,
            'signature' => sha1($toSign.$this->apiSecret),
        ];
    }
}
