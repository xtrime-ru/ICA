<?php


namespace App\Http\Controllers\Api\Sources;

use App\Http\Controllers\Api\ApiController;
use App\Models\Source;
use App\Models\SourceIcon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetIconController extends ApiController
{
    private const VALIDATION_RULES = [
        'id' => 'integer|required',
    ];
    public function index(Request $request): Response
    {
        $this->validate($request, static::VALIDATION_RULES);
        $user = $this->getUser();


        /** @var Source $source */
        $source = Source::query()
            ->select(['sources.*'])
            ->leftJoin('user_source as us', 'us.source_id', '=', 'id')
            ->whereRaw("sources.id = ? AND (us.user_id = ? OR sources.access = ?)", [
                $request->get('id'),
                $user?->id,
                'public'
            ])
            ->withOnly(['icon'])
            ->first()
        ;


        if (!$source->icon) {
            return response('', 404, [
                'Cache-Control' => 'max-age=86400',
            ]);
        }

        $meta = getimagesizefromstring($source->icon->refresh()->icon);
        return response($source->icon->icon, 200, [
            'Content-Type' => $meta['mime'],
            'Content-Length' => strlen($source->icon->icon),
            'Cache-Control' => 'max-age=86400',
        ]);
    }

}