<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Products point at a category by its name (products.category), so renaming a
 * category renames it on its products too.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $image_url
 * @property string|null $image_public_id
 * @property string|null $image_path
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'description', 'image_url', 'image_public_id', 'image_path', 'sort_order'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (Category $category): void {
            if ($category->wasChanged('name')) {
                Product::where('category', $category->getOriginal('name'))
                    ->update(['category' => $category->name]);
            }
        });
    }

    /**
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category', 'name');
    }

    /**
     * @param  Builder<Category>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * The uploaded photo, otherwise the bundled one, otherwise none.
     */
    public function imageSrc(): ?string
    {
        return $this->image_url ?? ($this->image_path !== null ? $this->image_path.'.jpg' : null);
    }

    /**
     * The bundled .webp twin, when the bundled photo is in use.
     */
    public function imageWebp(): ?string
    {
        return $this->image_url === null && $this->image_path !== null ? $this->image_path.'.webp' : null;
    }
}
