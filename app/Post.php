<?php

namespace App;

use App\Helpers\TextHelper;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Post
 *
 * @method static Builder|\App\Post newModelQuery()
 * @method static Builder|\App\Post newQuery()
 * @method static Builder|\App\Post query()
 * @mixin Eloquent
 * @property int $id
 * @property string $url
 * @property string|null $title
 * @property string|null $description
 * @property int $views
 * @property int $likes
 * @property int $bookmarks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereBookmarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereViews($value)
 * @property string|null $image
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereImage($value)
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Source[] $sources
 * @property-read int|null $sources_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Post whereCreatedAt($value)
 */
class Post extends Model
{
    protected static $unguarded = true;
    public const UPDATED_AT = null;

    private const TITLE_LENGTH = 100;
    private const DESCRIPTION_LENGTH = 750;
    private const TRIM_PLACEHOLDER = ' [...]';

    public function setTitleAttribute($text)
    {
        $text = TextHelper::htmlToText($text);
        $text = TextHelper::cropText($text, self::TITLE_LENGTH, static::TRIM_PLACEHOLDER);

        return $this->attributes['title'] = ($text ?: null);
    }

    public function setDescriptionAttribute($text)
    {
        $text = TextHelper::htmlToText($text);
        $text = TextHelper::cropText($text, self::DESCRIPTION_LENGTH, static::TRIM_PLACEHOLDER);

        return $this->attributes['description'] = ($text ?: null);
    }

    public function sources()
    {
        return $this->belongsToMany(Source::class, 'source_post');
    }
}
