<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Social
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @method static Builder|Social newModelQuery()
 * @method static Builder|Social newQuery()
 * @method static Builder|Social query()
 * @method static Builder|Social whereId($value)
 * @method static Builder|Social whereName($value)
 * @method static Builder|Social whereSlug($value)
 * @mixin \Eloquent
 */
class Social extends Model
{
    public $timestamps = false;
}
