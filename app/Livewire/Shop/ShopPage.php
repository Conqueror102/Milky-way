<?php

namespace App\Livewire\Shop;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The full catalogue, filterable by category. The home page shows a few products
 * per category and links here with "See all".
 *
 * @property-read Collection<int, Product> $products
 * @property-read list<string> $categories
 */
#[Layout('layouts::marketing')]
#[Title('Shop')]
class ShopPage extends Component
{
    #[Url(except: '')]
    public string $category = '';

    public function mount(): void
    {
        if ($this->category !== '' && ! in_array($this->category, $this->categories, true)) {
            $this->category = '';
        }
    }

    public function show(string $category): void
    {
        $this->category = in_array($category, $this->categories, true) ? $category : '';
    }

    /**
     * Category names that have products, in the admin's order; names no longer
     * in the Categories list follow at the end.
     *
     * @return list<string>
     */
    #[Computed]
    public function categories(): array
    {
        $inUse = Product::query()->active()->distinct()->pluck('category')
            ->filter()
            ->map(fn ($name): string => (string) $name)
            ->all();

        return array_values(Category::query()->ordered()->pluck('name')
            ->map(fn ($name): string => (string) $name)
            ->concat($inUse)
            ->unique()
            ->filter(fn (string $name): bool => in_array($name, $inUse, true))
            ->all());
    }

    /**
     * @return Collection<int, Product>
     */
    #[Computed]
    public function products(): Collection
    {
        return Product::query()
            ->active()
            ->when($this->category !== '', fn ($query) => $query->where('category', $this->category))
            ->ordered()
            ->get();
    }

    public function render(): View
    {
        /** @var \Illuminate\View\View $view */
        $view = view('livewire.shop.shop-page');

        return $view->layoutData([
            'description' => 'Shop skincare, beauty, body enhancement and spa products from Milkyway Cosmetics Stores, retail and wholesale, delivered across Lagos.',
        ]);
    }
}
