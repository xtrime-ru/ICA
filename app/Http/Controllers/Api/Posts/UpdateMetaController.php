<?php


namespace App\Http\Controllers\Api\Posts;


use App\Http\Controllers\Api\ApiController;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class UpdateMetaController extends ApiController
{

    private const VALIDATION_RULES = [
        'posts' => 'array|between:1,100',
        'posts.*.post_id' => 'integer|required',
        'posts.*.liked' => 'boolean|required',
        'posts.*.bookmarked' => 'boolean|required',
        'posts.*.viewed' => 'boolean|required',
    ];

    public function index(Request $request): array
    {
        if (!$user = $this->getUser()) {
            throw new UnauthorizedException('Необходимо зарегистрироваться или войти');
        }

        $this->validate($request, static::VALIDATION_RULES);

        $requestData = $request->get('posts');
        $requestData = array_combine(array_column($requestData, 'post_id'), array_values($requestData));

        $posts = Post::whereIn('id', array_keys($requestData))->get();

        $result = [];
        foreach ($posts as $post) {
            /** @var Post $post */
            $postMeta = $post->updateUserMeta($user, [
                'viewed' => $requestData[$post->id]['viewed'],
                'liked' => $requestData[$post->id]['liked'],
                'bookmarked' => $requestData[$post->id]['bookmarked'],
            ]);

            $result[] = [
                'post_id' => (int) $post->id,
                'views' => (int) $post->views,
                'viewed' => (bool) $postMeta->viewed,
                'likes' => (int) $post->likes,
                'liked' => (bool) $postMeta->liked,
                'bookmarks' => (int) $post->bookmarks,
                'bookmarked' => (bool) $postMeta->bookmarked,
            ];
        }

        return [
            'posts_meta' => $result
        ];
    }

}