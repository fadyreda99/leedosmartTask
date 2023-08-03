<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class TwoFactoryController extends Controller
{
    use ApiResponseTrait;
    public function store(Request $request)
    {
        $user = auth()->user();
        if($request->verify_code == $user->otp_code){
            $user->resetOtpCode();
            return $this->apiResponse(null, 'verified successfully', 200);
        }
        return $this->apiResponse(null, 'verified not successfully please try again later', 404);
    }
}
