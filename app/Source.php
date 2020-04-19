<?php

namespace App;

use App\Parsers\ParserRules;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Source
 *
 * @method static Builder|\App\Source newModelQuery()
 * @method static Builder|\App\Source newQuery()
 * @method static Builder|\App\Source query()
 * @mixin Eloquent
 * @property int $id
 * @property int|null $category_id
 * @property int|null $social_id
 * @property string $url
 * @property string $name
 * @property string $type
 * @property int $active
 * @property string $age_limit
 * @property int|null $user_id
 * @property int $likes
 * @property int $subscribers
 * @property int $views
 * @property string $parser_url
 * @property string $parser_type
 * @property ParserRules $parser_rules
 * @property \Illuminate\Support\Carbon|null $next_parse_at
 * @property string|null $parse_interval
 * @property \Illuminate\Support\Carbon|null $parsed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereAgeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereLikes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereNextParseAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereParseInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereParsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereParserRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereParserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereParserUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereSubscribers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereViews($value)
 * @property string $access
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Source whereAccess($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Post[] $posts
 * @property-read int|null $posts_count
 */
class Source extends Model
{
    protected $dates = [
        'next_parse_at',
        'parsed_at',
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
