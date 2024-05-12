<?php

namespace App\Http\Controllers;
use App\Models\User;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function redirect(Request $request){
       try{
            $email = $request->email;
            $password = $request->password;
            if (Auth::attempt(['email' => $email, 'password' => $password]) ) {
                $user = Auth::user();
                if($user->usertype == 0){
                    return view('userHome');
                }else{
                    return view('adminHome');
                }
            }
       }catch(Throwable $th){
        dd($th);
            return redirct()->back();
       }
    }

    public function login(){
        return view('login');
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
