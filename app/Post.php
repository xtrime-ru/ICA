<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Post
 *
 * @method static Builder|\App\Post newModelQuery()
 * @method static Builder|\App\Post newQuery()
 * @method static Builder|\App\Post query()
 * @mixin \Eloquent
 */
class Post extends Model
{

    protected static function boot()
    {
        parent::boot();
        /**
         * Event before save row to Db.
         *
         * @param Post $post
         */
        self::saving(
            static function (self $post) {
                //TODO trim url
            }
        );

        self::saved(
            static function (self $post) {

            }
        );
    }
}
