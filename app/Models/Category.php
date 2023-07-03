<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property array $style
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category query()
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereName($value)
 * @method static Builder|Category whereSlug($value)
 * @method static Builder|Category whereStyle($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    public $timestamps = false;
    protected $casts = [
        'style' => 'array'
    ];
}
