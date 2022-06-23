<?php

namespace App\Exceptions;

use Exception;
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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
        
         if (!$request->wantsJson() and !isAPI())
         return parent::render($request, $exception);

        $class = class_basename($exception);
        if (!method_exists($this, $class)) {
        return call_user_func([$this, 'generalException'], $exception);
        return parent::render($request, $exception);
        }
        return call_user_func([$this, $class], $exception);


    }




    protected function generalException($e)
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        $message = $e->getMessage();
        if (!$message && $statusCode == 404) {
            $message = "URI not found";
        }
        $details = method_exists($e, 'getDetails') ? $e->getDetails() : [];
        $payload = [
            'error' => [
                'status' => $statusCode,
                'name' => class_basename($e),
                'description' => $message,
                'details' => $details
            ]
        ];

        return $this->jsonResponse($payload);
    }

    /**
     * @SWG\Definition(
     *   definition="ValidationError",
     *   type="object",
     *   @SWG\Property(
     *     property="error",
     *     type="object",
     *     @SWG\Property(
     *       property="status",
     *       type="integer",
     *     ),
     *     @SWG\Property(
     *       property="name",
     *       type="string",
     *     ),
     *     @SWG\Property(
     *       property="description",
     *       type="string",
     *     ),
     *     @SWG\Property(
     *       property="details",
     *       type="object",
     *       @SWG\Property(
     *         property="field",
     *         type="array",
     *         @SWG\Items(
     *           type="string",
     *           description="error description"
     *         )
     *       )
     *     )
     *   )
     * )
     *
     * @SWG\Definition(
     *   definition="ModelNotFound",
     *   type="object",
     *   @SWG\Property(
     *     property="error",
     *     type="object",
     *     @SWG\Property(
     *       property="status",
     *       type="integer",
     *       example=500,
     *     ),
     *     @SWG\Property(
     *       property="name",
     *       type="string",
     *       example="ModelNotFoundException"
     *     ),
     *     @SWG\Property(
     *       property="description",
     *       type="string",
     *       example="No query results for model"
     *     ),
     *     @SWG\Property(
     *       property="details",
     *       type="array",
     *       @SWG\Items(
     *         type="string",
     *         description="error description"
     *       )
     *     )
     *   )
     * )
     */
    protected function ValidationException($e)
    {

        $payload = [
            'error' => [
                'status' => $e->status,
                'name' => class_basename($e),
                'description' => $e->getMessage(),
                'details' => array_flatten($e->validator->errors()->all())
            ]
        ];

        return $this->jsonResponse($payload);
    }

    protected function jsonResponse($payload)
    {
        return response()->json(
            $payload,
            $payload['error']['status']);
    }



}
