<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * One piece of home page content an admin has changed from its default.
 *
 * @property string $key
 * @property string|null $value
 * @property string|null $public_id
 * @property string|null $alt
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['key', 'value', 'public_id', 'alt'])]
class SiteContentEntry extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;
}
