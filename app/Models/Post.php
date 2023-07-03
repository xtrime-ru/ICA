<?php

namespace App\Models;

use App\Helpers\TextHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $url
 * @property string|null $title
 * @property string|null $description
 * @property string|null $image
 * @property int $views
 * @property int $likes
 * @property int $bookmarks
 * @property Carbon|null $created_at
 * @property-read Collection|Source[] $sources
 * @property-read int|null $sources_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereBookmarks($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereDescription($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereImage($value)
 * @method static Builder|Post whereLikes($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUrl($value)
 * @method static Builder|Post whereViews($value)
 * @property-read Collection|UserPost[] $userPost
 * @property-read int|null $user_post_count
 * @mixin \Eloquent
 */
class Post extends Model
{
    protected static $unguarded = true;
    public const UPDATED_AT = null;

    private const TITLE_LENGTH = 100;
    private const DESCRIPTION_LENGTH = 750;
    private const TRIM_PLACEHOLDER = ' [...]';

    public const META_COLUMNS = [
        'viewed' => 'views',
        'liked' => 'likes',
        'bookmarked' => 'bookmarks'
    ];

    public function title(): Attribute
    {
        return Attribute::make(
            set: function (string $text) {
                $text = TextHelper::htmlToText($text);
                $text = TextHelper::cropText($text, self::TITLE_LENGTH, static::TRIM_PLACEHOLDER);
                return ($text ?: null);
            }
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            set: function (string $text) {
                $text = TextHelper::htmlToText($text);
                $text = TextHelper::cropText($text, self::DESCRIPTION_LENGTH, static::TRIM_PLACEHOLDER);
                return ($text ?: null);
            }
        );
    }

    public function updateUserMeta(User $user, array $meta): UserPost {
        /** @var UserPost $postMeta */
        $postMeta = $this->userPost()->firstOrCreate([
            'user_id' => $user->id,
            'post_id' => $this->id,
        ]);

        foreach (static::META_COLUMNS as $metaColumn => $postColumn) {
            if (array_key_exists($metaColumn, $meta) && (bool)$meta[$metaColumn] !== (bool)$postMeta->{$metaColumn}) {
                if ($meta[$metaColumn]) {
                    $this->increment($postColumn);
                } else {
                    $this->decrement($postColumn);
                }
                $postMeta->{$metaColumn} = $meta[$metaColumn];
            }
        }
        if ($postMeta->isDirty()) {
            $postMeta->save();
        }

        return $postMeta;
    }

    public function sources()
    {
        return $this->belongsToMany(Source::class, 'source_post');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_post');
    }

    public function userPost() {
        return $this->hasMany(UserPost::class, 'post_id');
    }
}
