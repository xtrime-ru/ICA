<?php


namespace App\Http\Controllers\Api\Sources;

use App\Http\Controllers\Api\ApiController;
use App\Models\Category;
use App\Models\Source;
use App\Models\UserSource;

class GetController extends ApiController
{
    public function index(): array
    {
        $user = $this->authenticate();

        $sources = Source::query()
            ->select(['sources.*'])
            ->leftJoin('user_source as us', 'us.source_id', '=', 'id')
            ->orWhere('us.user_id', '=', $user->id)
            ->orWhere('access', '=', 'public')
            ->withOnly([])
            ->get()
        ;

        $categories = Category::query()->get();

        $enabledSources = UserSource::query()
            ->select(['source_id'])
            ->whereUserId($user->id)
            ->whereSelected(true)
            ->withOnly([])
            ->get()
        ;

        return [
            'sources' => $sources->toArray(),
            'categories' => $categories->toArray(),
            'sources_enabled' => array_column($enabledSources->toArray(), 'source_id'),
        ];
    }

}