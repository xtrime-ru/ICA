<?php


namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Api\ApiController;
use App\Post;
use App\Source;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

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

        if ($user = $this->getUser()) {
            $sources = Source::query()
                ->select(['sources.*'])
                ->join('user_source as us', 'us.source_id', '=', 'id')
                ->where('us.user_id', '=', $user->id)
                ->get()
            ;
        } else {
            $sources = Source::query()
                ->where('access', '=', 'public')
                ->get()
            ;
        }
        $sources = $sources->keyBy('id')->toArray();

        $query = Post::query()->from('posts', 'p');
        $query->select(['p.*', 'up.*', 'sp.source_id']);

        if ($id = (int) $request->get('id')) {
            $query->where('p.id','<', $id);
        }

        $query->join('source_post AS sp', static function (JoinClause $join) use($sources) {
            $join->on('p.id', '=', 'sp.post_id');
            $join->whereIn('sp.source_id', array_keys($sources));
        });

        $query->leftJoin('user_post AS up', static function(JoinClause $join) use($user) {
            $join->on('up.post_id', '=', 'p.id');
            $join->where('up.user_id', '=', $user->id ?? null);
        });

        if ($limit = (int) $request->get('limit')) {
            $query->limit($limit);
        } else {
            $query->limit(static::DEFAULT_LIMIT);
        }

        $query->orderBy('p.id', 'DESC');

        $posts = $query->get()->toArray();

        foreach ($posts as &$post) {
            $post['source'] = $sources[$post['source_id']];
        }
        unset($post);

        return [
            'posts' => $posts
        ];
    }

}