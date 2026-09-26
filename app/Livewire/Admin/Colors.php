<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\SiteContentEntry;
use App\Support\Palette;
use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Change the site's colours. The preview repaints as colours are picked;
 * nothing reaches the site until Save. A colour saved as its original is
 * removed, so the site goes back to the colours in app.css.
 */
#[Title('Colours')]
class Colors extends Component
{
    /** @var array<string, string> What the admin has picked, keyed by role. */
    public array $colors = [];

    /** Which page the preview shows: home, product or cart. */
    public string $page = 'home';

    /** CSS the preview applies to the page inside it. */
    #[Locked]
    public string $previewCss = '';

    public function mount(Palette $palette): void
    {
        $this->colors = $palette->colors();
        $this->refreshPreview($palette);
    }

    /**
     * Tidy a full colour code ("1F3D2B" becomes "#1f3d2b") so the colour
     * wheel can show it; half-typed codes are left alone.
     */
    public function updatedColors(mixed $value, string $role): void
    {
        if (preg_match('/^#?[0-9a-fA-F]{6}$/', trim((string) $value))) {
            $this->colors[$role] = (string) Palette::normalise((string) $value);
        }

        $this->resetValidation("colors.{$role}");
        $this->refreshPreview(app(Palette::class));
    }

    /**
     * Fill in a ready-made palette. It is saved with Save.
     */
    public function usePreset(string $preset, Palette $palette): void
    {
        $colors = $palette->presets()[$preset] ?? null;

        if ($colors === null) {
            return;
        }

        foreach (array_keys($palette->roles()) as $role) {
            $this->colors[$role] = $colors[$role];
        }

        $this->resetValidation();
        $this->refreshPreview($palette);
    }

    /**
     * Put one colour back to the original. It is saved with Save.
     */
    public function useOriginal(string $role, Palette $palette): void
    {
        $this->colors[$role] = $palette->original($role);
        $this->resetValidation("colors.{$role}");
        $this->refreshPreview($palette);
    }

    /**
     * Discard what hasn't been saved.
     */
    public function discard(Palette $palette): void
    {
        $this->colors = $palette->colors();
        $this->resetValidation();
        $this->refreshPreview($palette);
    }

    /**
     * Go straight back to the original colours, on the site too.
     */
    public function restoreOriginal(Palette $palette, SiteContent $content): void
    {
        SiteContentEntry::query()->where('key', 'like', Palette::KEY_PREFIX.'%')->delete();
        $content->flush();

        $this->colors = $palette->originals();
        $this->resetValidation();
        $this->refreshPreview($palette);

        session()->flash('status', __('The original colours are back on the site.'));
    }

    public function save(Palette $palette, SiteContent $content): void
    {
        $rules = [];

        foreach ($palette->roles() as $role => $info) {
            $rules["colors.{$role}"] = ['required', 'string', function (string $attribute, mixed $value, \Closure $fail) {
                if (Palette::normalise((string) $value) === null) {
                    $fail(__('Enter a colour code like #1f3d2b.'));
                }
            }];
        }

        $this->validate($rules, [], collect($palette->roles())->mapWithKeys(fn (array $info, string $role) => ["colors.{$role}" => strtolower($info['label'])])->all());

        foreach (array_keys($palette->roles()) as $role) {
            $color = (string) Palette::normalise($this->colors[$role]);
            $key = Palette::KEY_PREFIX.$role;

            if ($color === $palette->original($role)) {
                SiteContentEntry::query()->whereKey($key)->delete();
            } else {
                SiteContentEntry::updateOrCreate(['key' => $key], ['value' => $color]);
            }
        }

        $content->flush();
        $this->colors = $palette->colors();
        $this->refreshPreview($palette);

        session()->flash('status', __('Saved. The site shows your new colours now.'));
    }

    protected function refreshPreview(Palette $palette): void
    {
        $this->previewCss = $palette->css($this->picked($palette));
    }

    /**
     * The picks, with anything that isn't a colour yet left as it is on the site.
     *
     * @return array<string, string>
     */
    protected function picked(Palette $palette): array
    {
        return collect($palette->roles())
            ->map(fn (array $info, string $role) => Palette::normalise($this->colors[$role] ?? '') ?? $palette->color($role))
            ->all();
    }

    public function render(Palette $palette): View
    {
        $picked = $this->picked($palette);
        $product = Product::query()->where('is_active', true)->orderBy('sort_order')->first();

        $pages = array_filter([
            'home' => ['label' => __('Home'), 'url' => route('home')],
            'product' => $product ? ['label' => __('Product'), 'url' => route('products.show', $product)] : null,
            'cart' => ['label' => __('Cart'), 'url' => route('cart')],
        ]);

        $page = $pages[$this->page] ?? $pages['home'];

        return view('livewire.admin.colors', [
            'palette' => $palette,
            'picked' => $picked,
            'shades' => collect($palette->roles())->map(fn (array $info, string $role) => $palette->shades($role, $picked[$role]))->all(),
            'checks' => $palette->checks($picked),
            'activePreset' => collect($palette->presets())->search(fn (array $preset) => $preset['brand'] === $picked['brand'] && $preset['accent'] === $picked['accent'] && $preset['canvas'] === $picked['canvas']),
            'hasChanges' => collect($this->colors)->map(fn (string $color) => Palette::normalise($color) ?? $color)->all() !== $palette->colors(),
            'isCustom' => $palette->isCustom(),
            'pages' => $pages,
            'previewUrl' => $page['url'],
        ]);
    }
}
