<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Cloudinary;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

/**
 * @property-read Collection<int, ProductImage> $galleryImages
 */
class Form extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public string $name = '';

    public string $slug = '';

    public string $category = '';

    public string $type = '';

    public string $description = '';

    public string $price = '';

    public string $stock = '';

    public bool $is_active = true;

    /** @var UploadedFile|null */
    public $photo = null;

    /** @var array<int, UploadedFile> */
    public $photos = [];

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product;
            $this->name = $product->name;
            $this->slug = $product->slug;
            $this->category = $product->category;
            $this->type = (string) $product->type;
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
            'category' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:5120'],
            'photos' => ['array', 'max:10'],
            'photos.*' => ['image', 'max:5120'],
        ];
    }

    /**
     * Categories already in use, offered as suggestions.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function categories(): array
    {
        return Product::query()->distinct()->orderBy('category')->pluck('category')
            ->map(fn (mixed $category): string => (string) $category)
            ->values()
            ->all();
    }

    /**
     * The extra gallery photos already saved for this product.
     *
     * @return Collection<int, ProductImage>
     */
    #[Computed]
    public function galleryImages(): Collection
    {
        return $this->product?->images()->get() ?? new Collection;
    }

    public function removeUpload(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    /**
     * Swap a gallery photo with its neighbour. Direction is -1 (earlier) or 1 (later).
     */
    public function moveImage(int $imageId, int $direction): void
    {
        $images = $this->galleryImages->values();
        $from = $images->search(fn (ProductImage $image) => $image->id === $imageId);
        $to = $from === false ? false : $from + ($direction < 0 ? -1 : 1);

        if ($from === false || $to < 0 || $to >= $images->count()) {
            return;
        }

        $order = $images->all();
        [$order[$from], $order[$to]] = [$order[$to], $order[$from]];

        foreach (array_values($order) as $position => $image) {
            $image->update(['sort_order' => $position]);
        }

        unset($this->galleryImages);
    }

    public function deleteImage(int $imageId, Cloudinary $cloudinary): void
    {
        $image = $this->product?->images()->findOrFail($imageId);

        if ($image === null) {
            return;
        }

        if ($image->public_id !== null && $cloudinary->isConfigured()) {
            $cloudinary->destroy($image->public_id);
        }

        $image->delete();

        unset($this->galleryImages);
    }

    public function save(Cloudinary $cloudinary): void
    {
        $validated = $this->validate();

        $product = $this->product ?? new Product;
        $oldPublicId = $product->image_public_id;

        // Upload everything before touching the database, so a failed upload
        // leaves the product as it was and nothing orphaned in Cloudinary.
        $uploaded = [];

        try {
            foreach ([$this->photo, ...$this->photos] as $file) {
                $uploaded[] = $file === null ? null : $cloudinary->upload($file);
            }
        } catch (RuntimeException $e) {
            report($e);

            foreach (array_filter($uploaded) as $done) {
                $cloudinary->destroy($done['public_id']);
            }

            $this->addError($this->photo !== null && $uploaded === [] ? 'photo' : 'photos', __('A photo could not be uploaded. Check the Cloudinary settings and try again.'));

            return;
        }

        $main = array_shift($uploaded);

        if ($main !== null) {
            $product->image_url = $main['url'];
            $product->image_public_id = $main['public_id'];
        }

        $product->fill([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'category' => $validated['category'],
            'type' => $validated['type'] ?: null,
            'description' => $validated['description'] ?: null,
            'price' => filled($validated['price']) ? (int) $validated['price'] : null,
            'stock' => filled($validated['stock']) ? (int) $validated['stock'] : null,
            'is_active' => $validated['is_active'],
        ])->save();

        $nextPosition = ((int) $product->images()->max('sort_order')) + 1;

        foreach ($uploaded as $offset => $image) {
            $product->images()->create([
                'url' => $image['url'],
                'public_id' => $image['public_id'],
                'sort_order' => $nextPosition + $offset,
            ]);
        }

        if ($main !== null && $oldPublicId !== null) {
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
