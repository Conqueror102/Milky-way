<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Services\Cloudinary;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        return Product::query()
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->latest('id')
            ->paginate(15);
    }

    /**
     * Delete a product along with its Cloudinary image.
     */
    public function delete(int $productId, Cloudinary $cloudinary): void
    {
        $product = Product::findOrFail($productId);

        if ($cloudinary->isConfigured()) {
            $publicIds = $product->images()->pluck('public_id')->push($product->image_public_id)->filter();

            foreach ($publicIds as $publicId) {
                $cloudinary->destroy((string) $publicId);
            }
        }

        $product->delete();

        Flux::toast(variant: 'success', text: __(':name deleted.', ['name' => $product->name]));
    }

    public function render(): View
    {
        return view('livewire.admin.products.index');
    }
}
