<?php


namespace App\Http\Controllers\Api\Sources;

use App\Http\Controllers\Api\ApiController;
use App\Models\Category;
use App\Models\Post;
use App\Models\Source;
use App\Models\UserSource;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

final class ToggleController extends ApiController
{
    private const VALIDATION_RULES = [
        'source_id' => 'integer|required',
        'enabled' => 'boolean|required',
    ];

    public function index(Request $request): void
    {
        $this->validate($request, self::VALIDATION_RULES);
        $user = $this->authenticate();

        UserSource::query()->updateOrInsert([
            'user_id' => $user->id,
            'source_id' => $request->get('source_id'),
        ], [
            'selected' => $request->get('enabled'),
        ]);
    }

}