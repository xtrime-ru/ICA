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

use App\Helpers\RouterHelper;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation;

Route::any(
    '{path}',
    static function(string $path) {
        $pathElements = RouterHelper::parsePath($path);
        $controllers[] = RouterHelper::getController($pathElements, RouterHelper::API_NAMESPACE);
        $controllers[] = RouterHelper::getControllerFallback($pathElements, RouterHelper::API_NAMESPACE);
        $controller = RouterHelper::getFirstExistingController($controllers);

        if (!$controller) {
            $result = RouterHelper::response404();
        } else {
            try {
                $result = app()->call($controller);
            } catch (\Throwable $exception) {
                $result = RouterHelper::response400(
                    $exception->errors() ?? [$exception->getMessage()],
                    $exception->getCode()
                );
            }
        }

        if (!$result instanceof HttpFoundation\Response) {
            $result = response()->json($result);
        }

        if ($result instanceof HttpFoundation\JsonResponse) {
            $result->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $result;
    }
)->where('path', '.*');
