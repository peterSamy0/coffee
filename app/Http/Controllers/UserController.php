<?php

namespace App\Http\Controllers;

use App\Models\User;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    function __construct()
    {
        $this->middleware('auth:sanctum')->only('profile');
    }
    public function redirect(Request $request)
    {
        try {
            $email = $request->email;
            $password = $request->password;
            if (Auth::attempt(['email' => $email, 'password' => $password])) {
                $user = Auth::user();
                if ($user->usertype == 0) {
                    return view('userHome');
                } else {
                    return view('adminHome');
                }
            }
        } catch (Throwable $th) {
            dd($th);
            return redirct()->back();
        }
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


    public function register(Request $request)
    {
        try {
            // Create a new user
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->userType = 0;
            $user->phone = $request->phone;
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






    public function forgotPassword(Request $request)
    {

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
