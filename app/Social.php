<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Social
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @method static Builder|\App\Social newModelQuery()
 * @method static Builder|\App\Social newQuery()
 * @method static Builder|\App\Social query()
 * @mixin \Eloquent
 */
class Social extends Model
{
    public $timestamps = false;
}
