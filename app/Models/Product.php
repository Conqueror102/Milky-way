<?php

namespace App\Models;

use App\Support\Cart;
use App\Support\Money;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $category
 * @property string|null $type
 * @property string|null $description
 * @property int|null $price
 * @property string|null $image_url
 * @property string|null $image_public_id
 * @property string|null $image_path
 * @property int|null $stock
 * @property bool $is_active
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ProductImage> $images
 */
#[Fillable(['name', 'slug', 'category', 'type', 'description', 'price', 'image_url', 'image_public_id', 'image_path', 'stock', 'is_active', 'sort_order'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Extra gallery photos, after the main image.
     *
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Only products that are listed in the shop.
     *
     * @param  Builder<Product>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Shop order: the curated sort order, then name.
     *
     * @param  Builder<Product>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * The price the shop charges: the real price, or on preview deployments a stand-in
     * for products that don't have one yet (see config milkyway.shop.demo_prices).
     */
    public function sellingPrice(): ?int
    {
        if ($this->price !== null) {
            return $this->price;
        }

        return $this->usesDemoPrice() ? $this->demoPrice() : null;
    }

    public function usesDemoPrice(): bool
    {
        return $this->price === null && (bool) config('milkyway.shop.demo_prices');
    }

    /**
     * Whether this product can go in the cart: listed, priced and in stock.
     */
    public function isPurchasable(): bool
    {
        return $this->is_active && ($this->sellingPrice() ?? 0) > 0 && ! $this->isSoldOut();
    }

    public function isSoldOut(): bool
    {
        return $this->stock !== null && $this->stock <= 0;
    }

    /**
     * The most of this product one cart may hold.
     */
    public function maxOrderQuantity(): int
    {
        return $this->stock === null ? Cart::MAX_QUANTITY : min($this->stock, Cart::MAX_QUANTITY);
    }

    public function formattedPrice(): ?string
    {
        $price = $this->sellingPrice();

        return $price === null ? null : Money::format($price);
    }

    /**
     * The main image URL: the hosted upload when there is one, else the bundled JPEG.
     */
    public function imageSrc(): ?string
    {
        if (filled($this->image_url)) {
            return $this->image_url;
        }

        return filled($this->image_path) ? $this->image_path.'.jpg' : null;
    }

    /**
     * The bundled WebP twin, offered to browsers ahead of the JPEG. Hosted uploads have none.
     */
    public function imageWebp(): ?string
    {
        return blank($this->image_url) && filled($this->image_path) ? $this->image_path.'.webp' : null;
    }

    /**
     * Every photo for the product page: the main image first, then the gallery images.
     *
     * @return list<array{src: string, webp: string|null, alt: string}>
     */
    public function gallery(): array
    {
        $gallery = [];

        if ($src = $this->imageSrc()) {
            $gallery[] = ['src' => $src, 'webp' => $this->imageWebp(), 'alt' => $this->name];
        }

        foreach ($this->images as $image) {
            $gallery[] = [
                'src' => $image->url,
                'webp' => null,
                'alt' => $image->alt ?? $this->name.' photo '.(count($gallery) + 1),
            ];
        }

        return $gallery;
    }

    /**
     * A stable stand-in price between ₦2,500 and ₦22,000, the same on every page load.
     */
    private function demoPrice(): int
    {
        return (crc32($this->slug) % 40 + 5) * 500;
    }
}
