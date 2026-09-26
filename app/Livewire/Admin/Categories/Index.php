<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Models\Product;
use App\Services\Cloudinary;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @property-read Collection<int, Category> $categories
 */
#[Title('Categories')]
class Index extends Component
{
    /** The category being deleted, while the admin picks where its products go. */
    public ?int $deleting = null;

    public string $moveTo = '';

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()->ordered()->withCount('products')->get();
    }

    /**
     * Swap a category with its neighbour. Direction is -1 (earlier) or 1 (later).
     */
    public function move(int $categoryId, int $direction): void
    {
        $categories = $this->categories->values();
        $from = $categories->search(fn (Category $category) => $category->id === $categoryId);
        $to = $from === false ? false : $from + ($direction < 0 ? -1 : 1);

        if ($from === false || $to < 0 || $to >= $categories->count()) {
            return;
        }

        $order = $categories->all();
        [$order[$from], $order[$to]] = [$order[$to], $order[$from]];

        foreach (array_values($order) as $position => $category) {
            $category->update(['sort_order' => $position]);
        }

        unset($this->categories);
    }

    public function confirmDelete(int $categoryId): void
    {
        $this->deleting = $categoryId;
        $this->moveTo = '';
        $this->resetErrorBag();
    }

    public function cancelDelete(): void
    {
        $this->deleting = null;
    }

    /**
     * Delete a category. Its products, if any, move to the category the admin picked.
     */
    public function delete(Cloudinary $cloudinary): void
    {
        $category = Category::withCount('products')->findOrFail($this->deleting);

        if ($category->products_count > 0) {
            $this->validate([
                'moveTo' => ['required', 'string', 'exists:categories,name', 'not_in:'.$category->name],
            ], [
                'moveTo.required' => __('Pick a category for its :count products.', ['count' => $category->products_count]),
            ]);

            Product::where('category', $category->name)->update(['category' => $this->moveTo]);
        }

        if ($category->image_public_id !== null && $cloudinary->isConfigured()) {
            $cloudinary->destroy($category->image_public_id);
        }

        $category->delete();

        $this->deleting = null;
        unset($this->categories);

        Flux::toast(variant: 'success', text: __(':name deleted.', ['name' => $category->name]));
    }

    public function render(): View
    {
        return view('livewire.admin.categories.index');
    }
}
