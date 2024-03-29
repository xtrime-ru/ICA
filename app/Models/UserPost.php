<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserPost
 *
 * @property int $user_id
 * @property int $post_id
 * @property int $viewed
 * @property int $liked
 * @property int $bookmarked
 * @method static Builder|UserPost whereBookmarked($value)
 * @method static Builder|UserPost whereLiked($value)
 * @method static Builder|UserPost wherePostId($value)
 * @method static Builder|UserPost whereUserId($value)
 * @method static Builder|UserPost whereViewed($value)
 * @method static Builder|UserPost newModelQuery()
 * @method static Builder|UserPost newQuery()
 * @method static Builder|UserPost query()
 * @property Carbon|null $created_at
 * @method static Builder|UserPost whereCreatedAt($value)
 * @mixin \Eloquent
 */
class UserPost extends Model
{

    protected $table = 'user_post';

    protected static $unguarded = true;

    public $timestamps = false;

    protected $primaryKey = ['user_id', 'post_id'];
    public $incrementing = false;

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('user_id', $this->getAttribute('user_id'))
            ->where('post_id', $this->getAttribute('post_id'));
    }
}
