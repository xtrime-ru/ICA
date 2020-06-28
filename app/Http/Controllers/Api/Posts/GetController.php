<?php


namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Api\ApiController;
use App\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GetController extends ApiController
{
    private const VALIDATION_RULES = [
        'id' => 'integer',
        'limit' => 'integer|between:1,30'
    ];
    private const DEFAULT_LIMIT = 30;

    public function index(Request $request): array
    {
        $this->validate($request, static::VALIDATION_RULES);

        $now = microtime(true);
        $query = Post::query();

        if ($id = (int) $request->get('id')) {
            $query->where('id','<', $id);
        }

        if ($user = $this->getUser()) {
            $query->whereHas('sources', static function (Builder $query) use($user) {
                $query->orWhere('user_id', '=', $user->id);
                $query->orWhere('access', '=','public');
            });
        } else {
            $query->whereHas('sources', static function (Builder $query) {
                $query->where('access', '=','public');
            });
        }

        if ($limit = (int) $request->get('limit')) {
            $query->limit($limit);
        } else {
            $query->limit(static::DEFAULT_LIMIT);
        }

        $query->orderBy('id', 'DESC');

        $posts = $query->get()->toArray();

        Log::debug('get ', [
            'sql' => Str::replaceArray('?', $query->getQuery()->getBindings(), $query->getQuery()->toSql()),
            'time' => round(microtime(true) - $now, 3),
        ]);

        return [
            'posts' => $posts
        ];
    }

}