<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=>'required|unique:users',
            'phone_number'=>'required|unique:users',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return $this->apiResponse(null, $validator->errors(), 400);
        }

        $hashingPassword = Hash::make($request->password);

        $registerUser = User::create([
            'name'=>$request->name,
            'phone_number'=>$request->phone_number,
            'password'=>$hashingPassword
        ]);

        if($registerUser){
            $userRes = new UserResource($registerUser);
            return $this->apiResponse($userRes, 'user registered', 201);
        }else{
            return $this->apiResponse(null, 'user not registered', 400);
        }
    }
}
