<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use InvalidArgumentException;

/**
 * The site's colours: the three an admin picked at /admin/colors, or the
 * originals from config/palette.php. Every shade of a family is worked out from
 * the one colour picked, in OKLCH, so light and dark shades keep the same
 * relationship to it that the original shades had to the original colour.
 * Picks are stored as site content entries under "palette.<role>".
 */
class Palette
{
    public const KEY_PREFIX = 'palette.';

    public function __construct(protected SiteContent $content) {}

    /**
     * @return array<string, array{label: string, help: string, family: string, anchor: int, scale: array<int, string>, extras: array<string, string>}>
     */
    public function roles(): array
    {
        /** @var array<string, array{label: string, help: string, family: string, anchor: int, scale: array<int, string>, extras: array<string, string>}> */
        return config('palette.roles', []);
    }

    /**
     * @return array<string, array{label: string, brand: string, accent: string, canvas: string}>
     */
    public function presets(): array
    {
        /** @var array<string, array{label: string, brand: string, accent: string, canvas: string}> */
        return config('palette.presets', []);
    }

    /**
     * @return array<string, mixed>
     */
    public function role(string $role): array
    {
        return $this->roles()[$role] ?? throw new InvalidArgumentException("Unknown palette colour [{$role}].");
    }

    public function original(string $role): string
    {
        $info = $this->role($role);

        return $info['scale'][$info['anchor']];
    }

    /**
     * @return array<string, string>
     */
    public function originals(): array
    {
        return collect($this->roles())->map(fn (array $info, string $role) => $this->original($role))->all();
    }

    /**
     * The colour the site uses for a role now.
     */
    public function color(string $role): string
    {
        $saved = $this->content->entries()[self::KEY_PREFIX.$role]['value'] ?? null;

        return self::normalise((string) $saved) ?? $this->original($role);
    }

    /**
     * @return array<string, string>
     */
    public function colors(): array
    {
        return collect($this->roles())->map(fn (array $info, string $role) => $this->color($role))->all();
    }

    public function isCustom(): bool
    {
        return $this->colors() !== $this->originals();
    }

    /**
     * Every shade of a role's family for the given colour, keyed by CSS variable name.
     *
     * @return array<string, string>
     */
    public function shades(string $role, string $color): array
    {
        $info = $this->role($role);
        $color = self::normalise($color) ?? $this->original($role);
        $anchor = $info['scale'][$info['anchor']];
        $original = $color === $this->original($role);

        $shades = [];

        foreach ($info['scale'] as $step => $hex) {
            $shades["--color-{$info['family']}-{$step}"] = $original ? $hex : self::derive($color, $anchor, $hex);
        }

        foreach ($info['extras'] as $name => $hex) {
            $shades["--color-{$name}"] = $original ? $hex : self::derive($color, $anchor, $hex);
        }

        // The picked colour itself, exactly as chosen
        $shades["--color-{$info['family']}-{$info['anchor']}"] = $color;

        return $shades;
    }

    /**
     * CSS setting every variable for the given colours.
     *
     * @param  array<string, string>  $colors
     */
    public function css(array $colors): string
    {
        $vars = [];

        foreach (array_keys($this->roles()) as $role) {
            foreach ($this->shades($role, $colors[$role] ?? $this->original($role)) as $name => $value) {
                $vars[] = "{$name}:{$value}";
            }
        }

        return ':root{'.implode(';', $vars).'}';
    }

    /**
     * The <style> tag every page carries once an admin has changed the colours;
     * nothing at all while the originals from app.css are in use.
     */
    public function styleTag(): HtmlString
    {
        if (! $this->isCustom()) {
            return new HtmlString('');
        }

        return new HtmlString('<style id="site-palette">'.$this->css($this->colors()).'</style>');
    }

    /**
     * Whether the colours read well together, as WCAG contrast checks.
     *
     * @param  array<string, string>  $colors
     * @return array<int, array{label: string, ratio: float, needed: float, ok: bool, advice: string}>
     */
    public function checks(array $colors): array
    {
        $brand = self::normalise($colors['brand'] ?? '') ?? $this->original('brand');
        $accent = self::normalise($colors['accent'] ?? '') ?? $this->original('accent');
        $canvas = self::normalise($colors['canvas'] ?? '') ?? $this->original('canvas');

        $checks = [
            [__('Text in the main colour on the background'), self::contrast($brand, $canvas), 4.5, __('Pick a darker main colour or a lighter background.')],
            [__('White text on the main colour'), self::contrast('#ffffff', $brand), 4.5, __('Pick a darker main colour, so buttons and dark sections stay readable.')],
            [__('Highlight colour on the background'), self::contrast($accent, $canvas), 3.0, __('Pick a deeper highlight colour, so labels and icons stand out.')],
            // The hero and contact sections set big headings in the main colour on the highlight colour
            [__('Big headings in the main colour on the highlight colour'), self::contrast($brand, $accent), 3.0, __('Pick main and highlight colours further apart, one dark and one bright.')],
        ];

        return array_map(fn (array $check) => [
            'label' => $check[0],
            'ratio' => round($check[1], 1),
            'needed' => $check[2],
            'ok' => $check[1] >= $check[2],
            'advice' => $check[3],
        ], $checks);
    }

    /**
     * "#ABC", "abc" or "#aabbcc" as "#aabbcc"; null when it is not a colour.
     */
    public static function normalise(string $value): ?string
    {
        $value = strtolower(ltrim(trim($value), '#'));

        if (preg_match('/^[0-9a-f]{3}$/', $value)) {
            $value = $value[0].$value[0].$value[1].$value[1].$value[2].$value[2];
        }

        return preg_match('/^[0-9a-f]{6}$/', $value) ? '#'.$value : null;
    }

    /**
     * The shade that sits against $color as $shade sat against $anchor.
     */
    public static function derive(string $color, string $anchor, string $shade): string
    {
        [$pl, $pc, $ph] = self::toOklch($color);
        [$al, $ac, $ah] = self::toOklch($anchor);
        [$dl, $dc, $dh] = self::toOklch($shade);

        // Lightness moves the same share of the way towards white or black
        $l = $dl >= $al
            ? $pl + ($dl - $al) * (1 - $pl) / max(1 - $al, 1e-6)
            : $pl - ($al - $dl) * $pl / max($al, 1e-6);

        // Chroma keeps its ratio, hue its offset
        $c = min($ac > 1e-4 ? $pc * $dc / $ac : $dc, 0.37);
        $h = $ph + ($dh - $ah);

        return self::fromOklch(max(0.0, min(1.0, $l)), $c, $h);
    }

    /**
     * WCAG contrast ratio between two colours, from 1 to 21.
     */
    public static function contrast(string $a, string $b): float
    {
        $la = self::luminance($a);
        $lb = self::luminance($b);

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    public static function luminance(string $hex): float
    {
        [$r, $g, $b] = array_map(fn (int $v) => self::toLinear($v / 255), self::rgb($hex));

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    protected static function rgb(string $hex): array
    {
        $hex = self::normalise($hex) ?? throw new InvalidArgumentException("Not a colour: [{$hex}].");

        return [(int) hexdec(substr($hex, 1, 2)), (int) hexdec(substr($hex, 3, 2)), (int) hexdec(substr($hex, 5, 2))];
    }

    protected static function toLinear(float $v): float
    {
        return $v <= 0.04045 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
    }

    protected static function fromLinear(float $v): float
    {
        return $v <= 0.0031308 ? 12.92 * $v : 1.055 * ($v ** (1 / 2.4)) - 0.055;
    }

    /**
     * @return array{0: float, 1: float, 2: float} lightness 0–1, chroma, hue in degrees
     */
    protected static function toOklch(string $hex): array
    {
        [$r, $g, $b] = array_map(fn (int $v) => self::toLinear($v / 255), self::rgb($hex));

        $l = (0.4122214708 * $r + 0.5363325363 * $g + 0.0514459929 * $b) ** (1 / 3);
        $m = (0.2119034982 * $r + 0.6806995451 * $g + 0.1073969566 * $b) ** (1 / 3);
        $s = (0.0883024619 * $r + 0.2817188376 * $g + 0.6299787005 * $b) ** (1 / 3);

        $L = 0.2104542553 * $l + 0.7936177850 * $m - 0.0040720468 * $s;
        $A = 1.9779984951 * $l - 2.4285922050 * $m + 0.4505937099 * $s;
        $B = 0.0259040371 * $l + 0.7827717662 * $m - 0.8086757660 * $s;

        return [$L, sqrt($A * $A + $B * $B), rad2deg(atan2($B, $A))];
    }

    /**
     * @return array{0: float, 1: float, 2: float} linear sRGB, possibly out of range
     */
    protected static function oklchToLinear(float $L, float $C, float $H): array
    {
        $A = $C * cos(deg2rad($H));
        $B = $C * sin(deg2rad($H));

        $l = ($L + 0.3963377774 * $A + 0.2158037573 * $B) ** 3;
        $m = ($L - 0.1055613458 * $A - 0.0638541728 * $B) ** 3;
        $s = ($L - 0.0894841775 * $A - 1.2914855480 * $B) ** 3;

        return [
            4.0767416621 * $l - 3.3077115913 * $m + 0.2309699292 * $s,
            -1.2684380046 * $l + 2.6097574011 * $m - 0.3413193965 * $s,
            -0.0041960863 * $l - 0.7034186147 * $m + 1.7076147010 * $s,
        ];
    }

    /**
     * An OKLCH colour as hex, with chroma lowered until it fits on a screen.
     */
    protected static function fromOklch(float $L, float $C, float $H): string
    {
        $fits = fn (float $c): bool => collect(self::oklchToLinear($L, $c, $H))
            ->every(fn (float $v) => $v >= -1e-4 && $v <= 1 + 1e-4);

        if (! $fits($C)) {
            $low = 0.0;

            for ($i = 0; $i < 24; $i++) {
                $mid = ($low + $C) / 2;
                $fits($mid) ? $low = $mid : $C = $mid;
            }

            $C = $low;
        }

        return '#'.implode('', array_map(
            fn (float $v) => str_pad(dechex((int) round(max(0, min(1, self::fromLinear(max(0, $v)))) * 255)), 2, '0', STR_PAD_LEFT),
            self::oklchToLinear($L, $C, $H),
        ));
    }
}
