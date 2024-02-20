<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\Auth\RegisterRequest;
use App\Http\Requests\API\Auth\ForgotPasswordRequest;
use App\Mail\ForgotPassEmail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Request;
use Validator;

class AuthController extends AppBaseController
{

    public function register(RegisterRequest $request)
    {
        $validator = Validator::make($request->all(), $request->rules());

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }


    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user_id = $user->id;
            // var_dump($user->roles);

            // $result = DB::table('prod_user_details as usa')
            //     ->join('prod_user_roles as ur', 'usa.roles_id', '=', 'ur.user_roles_id')
            //     ->where('usa.status', '=', 'active')
            //     ->where('usa.user_id',  '=', $user_id)
            //     ->select('ur.*')
            //     ->get();

            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name']  =  $user->name;
            $success['roles'] =  $user->roles;
            $success['position'] =  $user->position;
            // $success['roles'] =  collect($result)->pluck('roles_code')->toArray();
            // $values =

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Password or Email Wrong.', 200);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        // Mail::to("dzakylmg87@gmail.com")->send(new ForgotPassEmail());
        $user = User::firstWhere('email', $request->email);

        // update user password
        $user->update(['password' => bcrypt($request->password)]);
    }
}
