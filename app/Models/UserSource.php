<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\UserSource
 *
 * @property int $user_id
 * @property int $source_id
 * @property bool $selected
 * @method static Builder|UserSource newModelQuery()
 * @method static Builder|UserSource newQuery()
 * @method static Builder|UserSource query()
 * @method static Builder|UserSource whereSelected($value)
 * @method static Builder|UserSource whereSourceId($value)
 * @method static Builder|UserSource whereUserId($value)
 * @mixin \Eloquent
 */
class UserSource extends Model
{

    protected $table = 'user_source';

    protected static $unguarded = true;

    public $timestamps = false;

    protected $primaryKey = ['user_id', 'source_id'];
    public $incrementing = false;

    protected function setKeysForSaveQuery($query)
    {
        return $query->where('user_id', $this->getAttribute('user_id'))
            ->where('source_id', $this->getAttribute('source_id'));
    }
}
