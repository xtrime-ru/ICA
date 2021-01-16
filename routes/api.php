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
use Symfony\Component\HttpFoundation\Response;

Route::any(
    '{path}',
    static function(string $path): Response {
        $pathElements = RouterHelper::parsePath($path);
        $controllers[] = RouterHelper::getController($pathElements, RouterHelper::API_NAMESPACE);
        $controllers[] = RouterHelper::getControllerFallback($pathElements, RouterHelper::API_NAMESPACE);
        try {
            $controller = RouterHelper::getFirstExistingController($controllers);
            $result = app()->call($controller);
        } catch (\BadMethodCallException $exception) {
            $result = RouterHelper::getErrorResponse(["Incorrect path: $path"], 404);
        } catch (\Throwable $exception) {
            $errors = RouterHelper::getExceptionErros($exception);
            $code = RouterHelper::getExceptionCode($exception);

            $result = RouterHelper::getErrorResponse($errors, $code);
        }

        $result = RouterHelper::formatResponse($result);

        return $result;
    }
)->where('path', '.*');
