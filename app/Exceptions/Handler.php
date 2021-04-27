<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Larabricks\OutputBuilder\OutputBuilder;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // auto rollback of all opened transactions
            while ( DB::transactionLevel() > 0 ) DB::rollBack();
        });
    }

    /**
     *  Method that render Unauthorized Response
     * @param $request
     * @param Throwable $e
     * @return mixed
     */
    public function render($request, Throwable $e): mixed
    {
        OutputBuilder::$code = ExceptionMapper::map($e);
        return OutputBuilder::build();
    }

}
