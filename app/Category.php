<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Category
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property array $style
 * @method static Builder|\App\Category newModelQuery()
 * @method static Builder|\App\Category newQuery()
 * @method static Builder|\App\Category query()
 * @mixin \Eloquent
 */
class Category extends Model
{
    public $timestamps = false;
    protected $casts = [
        'style' => 'array'
    ];
}
