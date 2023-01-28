<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helpers\XaraHelper;

class UserController extends Controller
{

    function create(Request $request){
        $messages = [
            'email.required'    => 'We need to know your e-mail address!',
            'email.unique'      => 'There already is an account with this email address',
            'email.email'       => 'Your email address is not in a correct format!',
            'password.min'      => 'The :attribute must be at least :min',
            'max'               =>  'The :attribute must be less than :max'
        ];
        $check= Validator::make($request->all(),
            ['email'=>'required|email|max:255|unique:users',
                'password' => 'required|string|min:4|max:255']
        ,$messages);
        if($check->fails()){
            return XaraHelper::makeAPIError($check->messages());
        }
        $user=User::create([
            'password'=>Hash::make($request->password),
            'email'=>$request->email
        ]);
        return ['status' => 'success','data'=>['id'=>$user['id']]];
    }

    function login(Request $request) {
        $messages = [
            'email.required'    => 'We need to know your e-mail address!',
            'email.unique'      => 'There already is an account with this email address',
            'email.email'       => 'Your email address is not in a correct format!',
            'password.min'      => 'The :attribute must be at least :min',
            'max'               =>  'The :attribute must be less than :max'

        ];
        $check= Validator::make($request->all(),
            ['email'=>'required|max:255|email',
                'password'=>'required|max:255']
            ,$messages);

        if($check->fails()){
            return XaraHelper::makeAPIError($check->messages());
        }

        $loginInfo=XaraHelper::setSession($request->email,$request->password);

        if(!empty($loginInfo)) {
            return ['status'=>'success','sessionId'=>$loginInfo['session']];
        }
        return XaraHelper::makeAPIError('Your email address or password is wrong');
    }

    function get(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }
        return XaraHelper::makeAPISuccess($user);
    }
}
