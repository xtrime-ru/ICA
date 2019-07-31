<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Symfony\Component\HttpFoundation;

Route::any(
    '{class}/{method}',
    static function($class, $method) {
        $className = '\\App\\Http\\Controllers\\Api\\';
        $className .= ucfirst($class) . 'Controller';

        try {
            $result = app()->call("$className@$method");
        } catch (\Exception $exception) {
            $result = response()
                ->json(['error' => $exception->getMessage()])
                ->setStatusCode(400)
            ;
        }

        if (!$result instanceof HttpFoundation\Response) {
            $result = response()->json($result);
        }

        if ($result instanceof HttpFoundation\JsonResponse) {
            $result->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $result;
    }
);

Route::fallback(
    static function() {
        return response()->json(['error' => 'Wrong api path'])
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ->setStatusCode(404)
        ;
    }
);
