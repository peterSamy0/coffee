<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckOtpRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\ResetPasswordVerificationNotification;

use Ichtrojan\Otp\Models\Otp as ModelsOtp;
use Ichtrojan\Otp\Otp;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    private $otp;

    function __construct()
    {
        $this->otp = new Otp();
        $this->middleware('auth:sanctum')->only('profile');
    }

    public function login(Request $request)
    {
        try {
            $email = $request->email;
            $password = $request->password;

            if (Auth::attempt(['email' => $email, 'password' => $password])) {
                $user = Auth::user();
                $token = $user->createToken('authToken')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }
        } catch (Throwable $th) {
            \Log::error('Login error: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    // public function register(Request $request)
    // {
    //     try {
    //         // Create a new user
    //         $user = new User;
    //         $user->name = $request->name;
    //         $user->email = $request->email;
    //         $user->phone = $request->phone;
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         return response()->json(['success' => 'Registration successful. Please log in'], 200);
    //     } catch (\Throwable $th) {
    //         // Log the exception message for debugging
    //         \Log::error('Error registering user: ' . $th->getMessage());
    //         return response()->json(['error' => $th->getMessage()], 400);
    //     }
    // }

    public function register(Request $request)
    {
        try {
            // Check if the email already exists
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json(['error' => 'Email already exists. Please use a different email.'], 400);
            }

            // Create a new user
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->usertype = 'user';
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['success' => 'Registration successful. Please log in'], 200);
        } catch (\Throwable $th) {
            // Log the exception message for debugging
            \Log::error('Error registering user: ' . $th->getMessage());
            return response()->json(['error' => $th->getMessage()], 400);
        }
    }



    public function profile(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to access your profile',
                ], 401);
            }
            if ($request->has('name') || $request->has('email') || $request->has('phone') || $request->has('password')) {
                $user->update($request->all());
            }
            return response()->json(['message' => $user], 201);

        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving your profile',
            ], 500);
        }
    }






    public function forgotPassword(ForgetPasswordRequest $request)
    {
        $input = $request->only('email');
        $user = User::where('email', $input['email'])->first();

        if ($user) {
            $user->notify(new ResetPasswordVerificationNotification());
            return response()->json(['message' => 'We have sent you a link to reset your password'], 200);
        }

        return response()->json(['message' => 'Email not found'], 404);
    }


    public function resetPassword(ResetPasswordRequest $request)
    {
        $otp2 = $this->otp->validate($request->email, $request->otp);
        if (!$otp2->status) {
            return response()->json(['error' => $otp2], 401);
        }
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        $user->tokens()->delete();
        $success['success'] = true;
        return response()->json($success, 200);
    }



}





// to insert new user open postman and enter the data like below
/*
    {
        "name": "admin",
        "email": "admin@mail.com",
        "phone": "01101201511",
        "password": "123456789"
    }
*/
//note the default user type is normal user if you want to insert admin you should change it from mysql.
