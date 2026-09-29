<?php

namespace App\Models;

use App\Support\Money;
use Database\Factories\DeliveryAreaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A place the shop delivers to, managed at /admin/delivery. Orders copy the name
 * and fee when they are placed, so changing or removing a place leaves them alone.
 *
 * @property int $id
 * @property string $name
 * @property int|null $fee
 * @property bool $is_featured
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'fee', 'is_featured', 'sort_order'])]
class DeliveryArea extends Model
{
    /** @use HasFactory<DeliveryAreaFactory> */
    use HasFactory;

    /**
     * How many places fit on the home page's delivery orbits.
     */
    public const MAX_FEATURED = 4;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fee' => 'integer',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @param  Builder<DeliveryArea>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * How the fee reads to a shopper: an amount, "Free", or arranged later.
     */
    public function feeLabel(): string
    {
        return match ($this->fee) {
            null => 'Arranged after payment',
            0 => 'Free',
            default => Money::format($this->fee),
        };
    }
}
