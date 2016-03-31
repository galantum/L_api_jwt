<?php

namespace App\Http\Middleware;

use Closure;

//import JWT Class
use \Firebase\JWT\JWT;
use Auth;

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

                #get value id from decoded_array
                $id = $decoded_array['id'];
                #get id logged user
                $idUserLogin = Auth::id();

                #create conditional statment if data value id match with value idUserLogin
                if ($id == $idUserLogin) {
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
}
