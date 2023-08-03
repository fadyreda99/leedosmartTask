<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserLoginResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use ApiResponseTrait;
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $user = User::where('name', $request->name)->first();

        if(!Hash::check($request->password, $user->password)){
            return $this->apiResponse(null, 'something is wrong try again', 400);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        //generate and insert otp code in db
        $user->generateOtpCode();

        //send sms to phone number using free package (vonage)
        $basic  = new \Vonage\Client\Credentials\Basic("6c3dc03b", "glMIWgGbFXHRpE2l");
        $client = new \Vonage\Client($basic);
        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS('2'.$user->phone_number, 'LtestOTP', 'your verification code is: '. $user->otp_code)
        );

        $loginRes = new UserLoginResource($user);
        return $this->apiResponse(['token' => $token, $loginRes], 'ok', 400);
    }
}
