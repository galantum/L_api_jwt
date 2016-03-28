<?php

namespace App\Http\Middleware;

use Closure;

class jwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        #create variable to get token from Authorization field
        $jwt = $request->header('Authorization');
        
        #create conditional statment if Token JWT null
        if (is_null($jwt)) {
            return response([ 'error' => 'Unauthorized.'], 401);
        } else {
            #Initialitated token signature
            $key = 'grandis';

            try{
                #decode Token JWT
                $decoded = JWT::decode($jwt, $key, array('HS256'));
                #Change decode Token JWT to array
                $decoded_array = (array)$decoded;

                #get value email and password from decoded_array
                $email = $decoded_array['email'];
                $password = $decoded_array['password'];

                #input value email and password in variable $data
                $data = ['email' => $email, 'password' => $password];

                #create conditional statment if Auth match with value $data
                if (Auth::attempt($data)) {
                    #Authentication success
                    return $next($request);
                } else {
                    #Authentication failed
                    return response([ 'error' => 'Unauthorized.'], 401);
                }

            }catch(\Exception $e){
                return response(['error' => $e->getMessage()], 500);
            }
    }
}
