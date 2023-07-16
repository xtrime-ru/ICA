<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SourceIcon
 *
 * @property int $source_id
 * @property string|null $icon
 * @method static Builder|SourceIcon newModelQuery()
 * @method static Builder|SourceIcon newQuery()
 * @method static Builder|SourceIcon query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SourceIcon whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SourceIcon whereSourceId($value)
 * @mixin \Eloquent
 */
class SourceIcon extends Model
{
    use HasFactory;

    protected $table = 'source_icon';

    protected $primaryKey = 'source_id';
    public $incrementing = false;

    protected static $unguarded = true;

    public $timestamps = false;


}
