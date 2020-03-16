<?php

namespace App\Http\Middleware;
use App\AccessTokens;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        $headers = $request->headers->all();
        if(empty($headers['x-access-token'][0]))
        {
            $data = [
                    "program" => "Test",
                    "release" => "V1",
                    "code" => 401,
                    "message" => "Token is missing.",
                    "ref" => "token_missing"
                ];
            
            array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
            return response()->json($data, 200, [], JSON_PRETTY_PRINT);
        }
        $token = $this->getAccessToken($request);
        $model = AccessTokens::where(['token' => $token])->first();
        
        if($model == null)
        {
            $data = [
                    "program" => "Test",
                    "release" => "V1",
                    "code" => 401,
                    "message" => "Token is invalid.",
                    "ref" => "token_invalid"
                ];
            array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
            return response()->json($data, 200, [], JSON_PRETTY_PRINT);
        }
        if($model['expires_at'] < time())
        {
            $data = [
                    "program" => "Test",
                    "release" => "V1",
                    "code" => 401,
                    "message" => "Token is expired.",
                    "ref" => "token_expired"
                ];
            array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
            return response()->json($data, 200, [], JSON_PRETTY_PRINT);
        }

        // if ($this->auth->guard($guard)->guest()) {
        //     return response('Unauthorized.', 401);
        // }
       
        return $next($request);
    }
    public function getAccessToken($request)
    {
        $headers = $request->headers->all();
        $token = false;
        if (!empty($headers['x-access-token'][0])) {

            $token = $headers['x-access-token'][0];

        } else if ($request->input('access_token')) {
            $token = $request->input('access_token');
        }

        return $token;
    }
    
}