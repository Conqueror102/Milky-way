<?php

namespace App\Support;

use App\Models\SiteContentEntry;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * The home page's editable photos and copy: what an admin saved, or the default
 * from config/site_content.php. Keys are "section.field", e.g. "hero.heading".
 */
class SiteContent
{
    public const CACHE_KEY = 'site-content';

    /** @var array<string, array{value: ?string, alt: ?string, public_id: ?string}>|null */
    protected ?array $entries = null;

    /**
     * @return array<string, array{label: string, description: string, anchor: ?string, groups: array<int, array{label: string, description?: string, fields: array<string, array<string, mixed>>}>}>
     */
    public function sections(): array
    {
        /** @var array<string, array{label: string, description: string, anchor: ?string, groups: array<int, array{label: string, description?: string, fields: array<string, array<string, mixed>>}>}> */
        return config('site_content.sections', []);
    }

    /**
     * Every field of a section, keyed by its full "section.field" key.
     *
     * @return array<string, array<string, mixed>>
     */
    public function fields(string $section): array
    {
        return collect($this->sections()[$section]['groups'] ?? [])
            ->flatMap(fn (array $group) => $group['fields'])
            ->mapWithKeys(fn (array $field, string $name) => ["{$section}.{$name}" => $field])
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function field(string $key): array
    {
        [$section] = explode('.', $key, 2);

        return $this->fields($section)[$key]
            ?? throw new InvalidArgumentException("Unknown site content key [{$key}].");
    }

    public function text(string $key): string
    {
        $field = $this->field($key);

        return $this->entries()[$key]['value'] ?? (string) $field['default'];
    }

    /**
     * Text with **double stars** turned into bold. Everything else is escaped.
     */
    public function rich(string $key, string $boldClass = 'font-semibold'): HtmlString
    {
        $html = preg_replace(
            '/\*\*(.+?)\*\*/s',
            '<span class="'.e($boldClass).'">$1</span>',
            e($this->text($key)),
        );

        return new HtmlString((string) $html);
    }

    /**
     * @return array<int, string>
     */
    public function lines(string $key): array
    {
        return self::splitLines($this->text($key));
    }

    /**
     * The uploaded photo's URL, or null while the bundled default is in use.
     */
    public function image(string $key): ?string
    {
        $this->field($key);

        return $this->entries()[$key]['value'] ?? null;
    }

    /**
     * The photo to show in the admin: the upload, or the bundled default.
     */
    public function imageUrl(string $key): string
    {
        return $this->image($key) ?? (string) $this->field($key)['default'];
    }

    public function alt(string $key): string
    {
        return $this->image($key) !== null
            ? (string) ($this->entries()[$key]['alt'] ?? '')
            : (string) ($this->field($key)['alt'] ?? '');
    }

    public function isCustom(string $key): bool
    {
        $this->field($key);

        return isset($this->entries()[$key]);
    }

    /**
     * A resized copy of a Cloudinary photo; other URLs are returned as they are.
     */
    public static function resized(string $url, int $width): string
    {
        if (! Str::contains($url, 'res.cloudinary.com/') || ! Str::contains($url, '/image/upload/')) {
            return $url;
        }

        return Str::replaceFirst('/image/upload/', "/image/upload/f_auto,q_auto,c_limit,w_{$width}/", $url);
    }

    /**
     * @return array<int, string>
     */
    public static function splitLines(string $text): array
    {
        return collect(preg_split('/\R/', $text) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter(fn (string $line) => $line !== '')
            ->values()
            ->all();
    }

    /**
     * Point the business details in config/milkyway.php at what an admin saved,
     * so the header, footer, SEO tags and checkout pick them up too.
     */
    public function applyToConfig(): void
    {
        $entries = $this->entries();
        $saved = fn (string $field): bool => isset($entries["business.{$field}"]);

        if ($saved('phone')) {
            $phone = $this->text('business.phone');

            config([
                'milkyway.phone' => $phone,
                'milkyway.phone_dial' => (str_starts_with($phone, '+') ? '+' : '').preg_replace('/\D/', '', $phone),
            ]);
        }

        if ($saved('whatsapp')) {
            config(['milkyway.whatsapp.number' => preg_replace('/\D/', '', $this->text('business.whatsapp'))]);
        }

        if ($saved('whatsapp_message')) {
            config(['milkyway.whatsapp.default_message' => $this->text('business.whatsapp_message')]);
        }

        if ($saved('address_line') || $saved('address_area')) {
            config([
                'milkyway.address.line' => $this->text('business.address_line'),
                'milkyway.address.area' => $this->text('business.address_area'),
                'milkyway.map_query' => $this->text('business.address_line').', '.$this->text('business.address_area'),
            ]);
        }

        if ($saved('hours')) {
            config(['milkyway.hours' => $this->text('business.hours')]);
        }

        if ($saved('delivery_areas')) {
            config(['milkyway.delivery_areas' => $this->lines('business.delivery_areas')]);
        }
    }

    /**
     * What admins have saved, cached until the next save.
     *
     * @return array<string, array{value: ?string, alt: ?string, public_id: ?string}>
     */
    public function entries(): array
    {
        if ($this->entries !== null) {
            return $this->entries;
        }

        try {
            /** @var array<string, array{value: ?string, alt: ?string, public_id: ?string}> $entries */
            $entries = Cache::rememberForever(self::CACHE_KEY, fn () => SiteContentEntry::query()
                ->get(['key', 'value', 'alt', 'public_id'])
                ->mapWithKeys(fn (SiteContentEntry $entry) => [$entry->key => [
                    'value' => $entry->value,
                    'alt' => $entry->alt,
                    'public_id' => $entry->public_id,
                ]])
                ->all());
        } catch (QueryException $e) {
            // Before the migration has run the site still renders, with its defaults.
            report($e);

            return [];
        }

        return $this->entries = $entries;
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);

        $this->entries = null;
    }
}
