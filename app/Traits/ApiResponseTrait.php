<?php

namespace App\Traits;

trait ApiResponseTrait
{
    public function apiResponse($data= null,$msg = null, $status=null ){
        $res = [
            'data' => $data,
            'msg' => $msg,
            'status' => $status
        ];

        return response($res, $status);
    }
}
