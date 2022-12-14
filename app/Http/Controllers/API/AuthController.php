<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $input = $request->only('name', 'username', 'email', 'password', 'password_confirmation');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;
        
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $username = $request->username;

        $inputs = [
            (filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username') => $username,
            'password' => $request->password,
        ];

        if (Auth::attempt($inputs)) { 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User login successfully.');
        } else { 
            return $this->sendError('Unauthorised.', ['error' => 'Login Failed'], 401);
        } 
    }
    
    /**
     * Get Profile api
     *
     * @return \Illuminate\Http\Response
     */
    public function get_profile(Request $request)
    {
        try {
            $user = $request->user();

            return $this->sendResponse([
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ], 'Profile information retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Error.', 'Something went wrong, please try again.', 400);
        }
    }

    /**
     * Post Profile api
     *
     * @return \Illuminate\Http\Response
     */
    public function post_profile(Request $request)
    {
        $input = $request->only('name', 'password');

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'nullable|min:8',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $user = $request->user();
        $user->name =  $input['name'];
        if ($request->filled('password')) {
            $user->password = Hash::make($input['password']);
        }
        $user->save();
        
        return $this->sendResponse([], 'Profile updated successfully.');
    }

    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            // // Revoke all tokens...
            // $request->user()->tokens()->delete();

            // Revoke the token that was used to authenticate the current request...
            $request->user()->currentAccessToken()->delete();

            return $this->sendResponse([], 'Logout successfully.');
        } catch (Exception $e) {
            return $this->sendError('Error.', 'Something went wrong, please try again.', 400);
        }
    }
    
    /**
     * Check Session api
     *
     * @return \Illuminate\Http\Response
     */
    public function check_session()
    {
        return $this->sendResponse([], 'Authenticated.');
    }

    /**
     * Forgot Password api
     *
     * @return \Illuminate\Http\Response
     */
    public function forgot_password(Request $request)
    {
        $input = $request->only('email');

        $validator = Validator::make($input, [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $response = Password::sendResetLink($input);

        try {
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    return $this->sendResponse([], 'Reset password notification is sent to your email.');
                    break;
                case Password::INVALID_USER:
                    return $this->sendError('Invalid Email.', 'Email not found.', 400);
                    break;
                default:
                    return $this->sendError('Error.', 'You need to wait a few minutes before requesting a reset password notification link.', 400);
                    break;
            }
        } catch (Exception $e) {
            return $this->sendError('Error.', 'Something went wrong, please try again.', 400);
        }
    }
    
    /**
     * Reset Password api
     *
     * @return \Illuminate\Http\Response
     */
    public function reset_password(Request $request)
    {
        $input = $request->only('email', 'token', 'password', 'password_confirmation');

        $validator = Validator::make($input, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $response = Password::reset($input, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        try {
            switch ($response) {
                case Password::PASSWORD_RESET:
                    return $this->sendResponse([], 'Password reset successfully.');
                    break;
                case Password::INVALID_TOKEN:
                    return $this->sendError('Invalid Token.', 'Token not found.', 400);
                    break;
                default:
                    return $this->sendError('Error.', 'Something went wrong, please try again.', 400);
                    break;
            }
        } catch (Exception $e) {
            return $this->sendError('Error.', 'Something went wrong, please try again.', 400);
        }
    }
}
