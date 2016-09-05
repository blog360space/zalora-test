<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\HttpStatusCode;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 *
 * @SWG\Swagger(
 *     basePath="/research/laravel5/zaltest/public/api",
 *     schemes={"http"},
 *     @SWG\Info(
 *         version="0.8.24",
 *         title="Zalora test API",
 *         @SWG\Contact(name="HungTran", email="mhungou04@gmail.com"),
 *     )
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * @param boolean $result
     * @param string|array $data
     * @param int $statusCode
     * @return \Phalcon\HTTP\ResponseInterface
     */
    protected function response(
        $data,
        $errorCode = 0,
        $status = HttpStatusCode::OK
    )
    {
        // success
        if ($status == HttpStatusCode::OK) {
            $dataResponse = [
               
                'data' => $data,
                'code' => $errorCode
            ];
        }
        //false
        else {
            $dataResponse = [
                'message' => $data,
                'code' => $errorCode
            ];
        }
        
        $status = $status == 0 ? HttpStatusCode::SERVICE_UNAVAILABLE : $status;
        
        return response($dataResponse, $status, ['Content-Type' => 'application/json',
            'charset' => 'utf-8']);
    }
}
