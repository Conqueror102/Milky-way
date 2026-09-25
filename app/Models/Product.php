<?php

namespace App\Models;

use App\Support\Money;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property int $stock
 * @property bool $is_active
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
     * Whether this product can go in the cart. Products without a price are enquiry-only.
     */
    public function isPurchasable(): bool
    {
        return $this->is_active && $this->price !== null && $this->price > 0;
    }

    public function formattedPrice(): ?string
    {
        return $this->price === null ? null : Money::format($this->price);
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
}
