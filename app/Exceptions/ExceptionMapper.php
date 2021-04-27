<?php


namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use \Throwable as Throwable;

class ExceptionMapper
{

    /**
     *  Info: Insert here the Exception class and the code to return as response
     *  Array to map an Exception class namespace and a status code
     *  @var int[]
     */
    private static array $mapping = [
        // 400
        "Illuminate\Validation\ValidationException" => 400,
        'Symfony\Component\HttpKernel\Exception\BadRequestHttpException' => 400,
        // 401
        "App\Exceptions\AuthenticationFailedException" => 401,
        // 403
        "App\Exceptions\ForbiddenException" => 403,
        // 404
        "Illuminate\Database\Eloquent\ModelNotFoundException" => 404,
        "Symfony\Component\HttpKernel\Exception\NotFoundHttpException" => 404,
        "Illuminate\Database\QueryException:23000:1452" => 404,
        // 405
        "Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException" => 405,
        // 409
        "Illuminate\Database\QueryException:23000:1062" => 409,
    ];

    /**
     *  Method that map an Exception and a status code using the '$mapping' array
     *  @param Throwable $exc
     *  @return int
     */
    public static function map( Throwable $exc ): int
    {
        self::_log($exc);
        $class = get_class($exc);
        // get the type code or null
        $typeCode = ( $class === 'Illuminate\Database\QueryException' ) ? $exc?->errorInfo['1'] : null;
        // Get the class of exception and log it
        $code = $exc->getCode();
        // calculate the possible $key
        $keyCodeAndTypeCode = "${class}:${code}:${typeCode}";
        $keyCode = "${class}:${code}";
        $code = self::$mapping[$keyCodeAndTypeCode] ?? null;
        $code = ( $code === null ) ? self::$mapping[$keyCode] ?? null : $code;
        $code = ( $code === null ) ? self::$mapping[$class] ?? 500 : $code;
        return $code;
    }

    /**
     *  Method that print in 'laravel.log' file the Exception infos
     *  @param Throwable $exc
     *  @return void
     */
    private static function _log( Throwable $exc )
    {
        Log::debug("EXCEPTION CLASS");
        Log::debug(get_class($exc));
        Log::debug("EXCEPTION TRACE");
        Log::debug($exc);
        Log::debug(PHP_EOL . PHP_EOL);
    }

}
