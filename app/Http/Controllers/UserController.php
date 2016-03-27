<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//import Input Class
use Illuminate\Support\Facades\Input;

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

            #create payload for token JWT
            $payload = array(
                #create expired time for token (Token Expired after 30 minutes)
                "exp" => time() + 1800
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
}
