<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//import Input Class
use Illuminate\Support\Facades\Input;
//import Auth Class
use Auth;
//import JWT Class
use \Firebase\JWT\JWT;

class UserController extends Controller
{
    //

    #Create method for register
    public function register(){
    	#get all data from input
    	$data = Input::all();
        
        #create validation
        $validation = Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        #create conditional statment if validation passes
        if ($validation->passes()) {
            #create data user in table
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            #create json respon success
            return response(json_encode('Registrasi telah berhasil', 200));
        } else {
        	#create json respon failed
            return response(json_encode(['error' => 'Registrasi gagal'], 406));
        }       

    }

    public function login(){
    	#get al data from form input
    	$data = Input::all();

    	#create conditional statment if $data match one user table data
        if(Auth::attempt($data)){
            #create signature for token JWT
            $key = 'grandis';

            #get id from logged user
            $id = Auth::id();

            #create payload for token JWT
            $payload = array(
                #create expired time for token (Token Expired after 30 minutes)
                "exp" => time() + 1800,
                /*#create email from value email input
                "email" => $data['email'],
                #create password from value pasword input
                "password" => $data['password']*/

                #create iss
                "iss" => "http://localhost:8000/",

                #create subject
                "sub" => "Access API",

                #create id from value $id
                "id" => $id;
            );

            #generate token jwt
            $jwt = JWT::encode($payload, $key);

            #create json response with token JWT and success
            return response(json_encode(['jwt' => $jwt], 200));
        } else {
        	#create json responce failed
            return response(json_encode(['error' => 'Username and password wrong'], 500));
        }		
    }

    public function show(){
        #Find id logged user
        $id = Auth::id();

        #show all data from User when id match with $id
        return User::find($id);
    }

    public function edit(){
        #get id logged user
        $id = Auth::id();

        #get all value from form input
        $input = Input::only('name', 'password');

        #create validation
        $validation = Validator::make($input, [
            'name' => 'required|max:255',
            'password' => 'required|min:6',
        ]);

        #create condition statment if validation passes
        if ($validation->passes())
        {
            #get all data from User when id match with $id
            $user = User::find($id);
            #update data user with new value from value input
            $user->update([
                'name' => $input['name'],
                'password' => bcrypt($input['password']),
            ]);
            #create json response Update success
            return response(json_encode('Update success', 200));
        }
        #create json response Update failed
        return response(json_encode('Update failed', 500));
    }

    public function resetToken(){
        #get id from logged user
        $id = Auth::id();

        if (isset($id)) {
            #create signature for token JWT
            $key = 'grandis';

            #create payload for token JWT
            $payload = array(
                #create expired time for token (Token Expired after 30 minutes)
                "exp" => time() + 1800,
                #create iss
                "iss" => "http://localhost:8000/",
                #create subject
                "sub" => "Access API",
                #create id from value $id
                "id" => $id;
            );

            #generate token jwt
            $jwt = JWT::encode($payload, $key);

            #create json response with token JWT and success
            return response(json_encode(['jwt' => $jwt], 200));
        } else {
            return response(json_encode(['error' => 'Unauthorized.'], 401));
        }
    }

    public function logout(){
        #user logout
        Auth::logout();

        #create conditional statment if user is logout
        if (Auth::check() == FALSE) {
            return response(json_encode(['User have been log out'], 200));
        } else {
            return response(json_encode(['error' => 'Unauthorized.'], 401));
        }
    }
}
