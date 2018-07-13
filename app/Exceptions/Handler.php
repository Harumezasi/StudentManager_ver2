<?php

namespace App\Exceptions;

use App\Http\Controllers\ResponseObject;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof NotValidatedException) {
            // 유효하지 않은 요청 예외처리
            $message = $exception->getMessage();
            $jsonMessage = json_decode($message);
            if(json_last_error() == JSON_ERROR_NONE) {
                $message = $jsonMessage;
            }

            return response()->json(new ResponseObject(
                false, $message
            ), 400);
        } else if($exception instanceof ModelNotFoundException) {
            // 데이터가 검색되지 않는 경우
            return response()->json(new ResponseObject(
                false, __('response.data_not_found')
            ), 500);
        }

        return parent::render($request, $exception);
    }
}
