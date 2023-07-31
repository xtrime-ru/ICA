<?php

namespace App\Models;

use App\Enums\SourceTypes;
use App\Parsers\ParserRules;
use App\Parsers\ParserRulesCast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;


/**
 * App\Models\Source
 *
 * @property int $id
 * @property int|null $category_id
 * @property string $url
 * @property string $name
 * @property string $access
 * @property bool $active
 * @property int $age_limit
 * @property int|null $user_id
 * @property int $likes
 * @property int $subscribers
 * @property int $views
 * @property string $parser_url
 * @property SourceTypes $type
 * @property ParserRules $parser_rules
 * @property Carbon|null $next_parse_at
 * @property int|null $parse_interval minutes
 * @property Carbon|null $parsed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Post> $posts
 * @property-read SourceIcon|null $icon
 * @property-read int|null $posts_count
 * @method static Builder|Source newModelQuery()
 * @method static Builder|Source newQuery()
 * @method static Builder|Source query()
 * @method static Builder|Source whereAccess($value)
 * @method static Builder|Source whereActive($value)
 * @method static Builder|Source whereAgeLimit($value)
 * @method static Builder|Source whereCategoryId($value)
 * @method static Builder|Source whereCreatedAt($value)
 * @method static Builder|Source whereIcon($value)
 * @method static Builder|Source whereId($value)
 * @method static Builder|Source whereLikes($value)
 * @method static Builder|Source whereName($value)
 * @method static Builder|Source whereNextParseAt($value)
 * @method static Builder|Source whereParseInterval($value)
 * @method static Builder|Source whereParsedAt($value)
 * @method static Builder|Source whereParserRules($value)
 * @method static Builder|Source whereParserType($value)
 * @method static Builder|Source whereParserUrl($value)
 * @method static Builder|Source whereSocial($value)
 * @method static Builder|Source whereSubscribers($value)
 * @method static Builder|Source whereUpdatedAt($value)
 * @method static Builder|Source whereUrl($value)
 * @method static Builder|Source whereUserId($value)
 * @method static Builder|Source whereViews($value)
 * @property-read string|null $icon_url
 * @mixin \Eloquent
 */
class Source extends Model
{

    protected $appends = [
        'icon_url',
    ];
    /**
     * The attributes that should be visible in serialization.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'category_id',
        'type',
        'url',
        'icon_url',
        'name',
        'age_limit',
        'likes',
        'subscribers',
        'views',
    ];

    protected $casts = [
        'active' => 'bool',
        'age_limit' => 'integer',
        'next_parse_at' => 'date',
        'parsed_at' => 'date',
        'type' => SourceTypes::class,
    ];

    public function parserRules(): Attribute
    {
        return new Attribute(
            get: fn(?string $value): ParserRules => new ParserRules($this->type, $value),
            set: fn(string|ParserRules|null $value): ?string => (string)$value ?: null,
        );
    }

    public function iconUrl(): Attribute
    {
        return new Attribute(
            get: fn($value): ?string => $this->icon ? "/api/sources/getIcon/?" .  http_build_query(['id' => $this->id]) : null,
        );
    }

    /**
     * @return BelongsToMany<Post>
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'source_post');
    }

    /**
     * @return HasOne<SourceIcon>
     */
    public function icon(): HasOne
    {
        return $this->hasOne(SourceIcon::class, 'source_id', 'id')->select('source_id');
    }

}
