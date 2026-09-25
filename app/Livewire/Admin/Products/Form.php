<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Services\Cloudinary;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

class Form extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public string $price = '';

    public string $stock = '0';

    public bool $is_active = true;

    /** @var UploadedFile|null */
    public $photo = null;

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product;
            $this->name = $product->name;
            $this->slug = $product->slug;
            $this->description = (string) $product->description;
            $this->price = (string) $product->price;
            $this->stock = (string) $product->stock;
            $this->is_active = $product->is_active;
        }
    }

    /**
     * Keep the slug in step with the name until it is edited by hand.
     */
    public function updatedName(string $value): void
    {
        if ($this->product === null) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($this->product)],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function save(Cloudinary $cloudinary): void
    {
        $validated = $this->validate();

        $product = $this->product ?? new Product;
        $oldPublicId = $product->image_public_id;

        if ($this->photo !== null) {
            try {
                $image = $cloudinary->upload($this->photo);
            } catch (RuntimeException $e) {
                report($e);
                $this->addError('photo', __('The image could not be uploaded. Check the Cloudinary settings and try again.'));

                return;
            }

            $product->image_url = $image['url'];
            $product->image_public_id = $image['public_id'];
        }

        $product->fill([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?: null,
            'price' => filled($validated['price']) ? (int) $validated['price'] : null,
            'stock' => (int) $validated['stock'],
            'is_active' => $validated['is_active'],
        ])->save();

        if ($this->photo !== null && $oldPublicId !== null) {
            $cloudinary->destroy($oldPublicId);
        }

        session()->flash('status', __('Product saved.'));

        $this->redirectRoute('admin.products.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.products.form')
            ->title($this->product ? __('Edit product') : __('New product'));
    }
}
