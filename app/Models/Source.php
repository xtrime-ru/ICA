<?php

namespace App\Models;

use App\Parsers\ParserRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


/**
 * App\Models\Source
 *
 * @property int $id
 * @property int|null $category_id
 * @property int|null $social_id
 * @property string $url
 * @property string|null $icon
 * @property string $name
 * @property string $access
 * @property bool $active
 * @property string $age_limit
 * @property int|null $user_id
 * @property int $likes
 * @property int $subscribers
 * @property int $views
 * @property string $parser_url
 * @property string $parser_type
 * @property ParserRules $parser_rules
 * @property Carbon|null $next_parse_at
 * @property int|null $parse_interval minutes
 * @property Carbon|null $parsed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Post[] $posts
 * @property-read int|null $posts_count
 * @method static Builder|Source newModelQuery()
 * @method static Builder|Source newQuery()
 * @method static Builder|Source query()
 * @method static Builder|Source whereAccess($value)
 * @method static Builder|Source whereActive($value)
 * @method static Builder|Source whereAgeLimit($value)
 * @method static Builder|Source whereCategoryId($value)
 * @method static Builder|Source whereCreatedAt($value)
 * @method static Builder|Source whereId($value)
 * @method static Builder|Source whereLikes($value)
 * @method static Builder|Source whereName($value)
 * @method static Builder|Source whereNextParseAt($value)
 * @method static Builder|Source whereParseInterval($value)
 * @method static Builder|Source whereParsedAt($value)
 * @method static Builder|Source whereParserRules($value)
 * @method static Builder|Source whereParserType($value)
 * @method static Builder|Source whereParserUrl($value)
 * @method static Builder|Source whereSocialId($value)
 * @method static Builder|Source whereSubscribers($value)
 * @method static Builder|Source whereUpdatedAt($value)
 * @method static Builder|Source whereUrl($value)
 * @method static Builder|Source whereIcon($value)
 * @method static Builder|Source whereUserId($value)
 * @method static Builder|Source whereViews($value)
 * @mixin \Eloquent
 */
class Source extends Model
{
    protected $dates = [
        'next_parse_at',
        'parsed_at',
    ];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'category_id',
        'social_id',
        'url',
        'icon',
        'name',
        'age_limit',
        'likes',
        'subscribers',
        'views',
    ];

    /**
     * @param $parserRules
     *
     * @return ParserRules
     */
    public function getParserRulesAttribute($parserRules): ParserRules
    {
        if (!($parserRules instanceof ParserRules)) {
            $parserRules = new ParserRules(json_decode($parserRules, true));
            $this->parser_rules = $parserRules;
        }

        return $parserRules;
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'source_post');
    }
}
