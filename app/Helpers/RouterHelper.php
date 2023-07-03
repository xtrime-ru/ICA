<?php


namespace App\Helpers;


use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RouterHelper
{

    public const API_NAMESPACE =  '\\App\\Http\\Controllers\\Api';
    private const INDEX_METHOD = 'index';

    public static function parsePath($path): array
    {
        $pathElements = explode('/', $path);
        $pathElements = array_map('ucfirst', $pathElements);

        return $pathElements;
    }

    /**
     * @param array $pathElements
     * @param string $namespace
     *
     * @param string|null $indexMethod
     *
     * @return string[]
     */
    public static function getController(array $pathElements, string $namespace, ?string $indexMethod = null): array
    {
        if ($indexMethod) {
            $method = $indexMethod;
        } else {
            $method = lcfirst(array_pop($pathElements));
        }

        $controller = array_pop($pathElements) . 'Controller';
        $classElements = array_merge([$namespace], $pathElements, [$controller]);
        $classPath = implode('\\', $classElements);


        return [$classPath, $method];
    }

    public static function getControllerFallback(array $pathElements, string $namespace) {
        return static::getController($pathElements, $namespace, static::INDEX_METHOD);
    }

    /**
     * @param $controllers
     *
     * @return string|null
     */
    public static function getFirstExistingController($controllers): ?string
    {
        foreach ($controllers as [$class, $method]) {
            if (method_exists($class, $method)) {
                return "{$class}@{$method}";
            }
        }
        throw new \BadMethodCallException("Method not found: {$method}", 404);
    }

    public static function getErrorResponse(array $errors, int $code = 0): JsonResponse
    {
        return response()
            ->json(['errors' => $errors])
            ->setStatusCode($code)
        ;
    }

    public static function getExceptionErros(\Throwable $throwable): array
    {
        if (method_exists($throwable, 'errors')) {
            $errors = $throwable->errors();
        } else {
            $errors = [$throwable->getMessage()];
        }

        return $errors;
    }

    public static function getExceptionCode(\Throwable $throwable): int
    {
        $code = $throwable->getCode();
        if ($throwable instanceof AuthenticationException) {
            $code = 401;
        }
        if ($throwable instanceof \PDOException || !is_int($code)) {
            $code = 500;
        }

        if ($code < 300 || $code >= 600 ) {
            $code = 400;
        }

        return $code;
    }

    public static function formatResponse(Response|array|null $response): Response
    {
        if (!$response instanceof Response) {
            $response = response()->json($response);
        }

        if ($response instanceof JsonResponse) {
            $response->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $response;
    }


}
