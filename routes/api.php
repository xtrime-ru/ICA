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
    '/{class}/{method}',
    static function(Illuminate\Http\Request $request, $class, $method) {
        $className = '\\App\\Http\\Controllers\\Api\\';
        $className .= ucfirst(mb_strtolower($class)) . 'Controller';

        try {
            if (class_exists($className)) {
                $class = new $className;
                if (method_exists($class, $method) && is_callable([$class, $method])) {
                    $result = $class->{$method}($request);
                    if (!$result) {
                        throw new UnexpectedValueException('Empty result');
                    }
                } else {
                    throw new UnexpectedValueException('Unknown class');
                }
            } else {
                throw new UnexpectedValueException('Unknown method');
            }
        } catch (\Exception $exception) {
            $result = response()
                ->json(['error' => $exception->getMessage()])
                ->setStatusCode(404)
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

Route::any(
    '{any?}',
    static function() {
        return response()->json(['error' => 'Wrong api path'])
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            ->setStatusCode(404)
            ;
    }
)->where('any', '.*')
;
