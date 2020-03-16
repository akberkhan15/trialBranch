<?php
namespace App\Library\Services;

use App\User;
use App\AuthorizationCodes;
use App\AccessTokens;
use Illuminate\Support\Facades\Hash;
use App\Plaza;

class UserService {
    //To login 
    public static function login($request)
    {
        if ($model = User::authorize($request->all())) {
            $auth_code = static::createAuthorizationCode($model->id); 

            $data = [];
            $data['authorization_code'] = $auth_code->code;
            $data['expires_at'] = $auth_code->expires_at;
            $status = 200;
            $response = array(
                'status'    => 'SUCCESS',
                'message'   => "Login Successfully.",
                'ref'     => 'login_successfully',
                'data'    => $data,
            );
        } else 
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => "Username or Password is wrong",
                'ref'     => "invalid_crediatianls",
            );
        }
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    } 

    //To get the access token
    public function accesstoken($request)
    {
        $attributes = $request->all();
        $auth_code = AuthorizationCodes::isValid($attributes['authorization_code']);
        if (!$auth_code) 
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => "Invalid Authorization Code",
                'ref'     => "invalid_authorization_code",
            );
        }
        else
        {
            $model = static::createAccesstoken($attributes['authorization_code']);
            $data = [];
            $data['access_token'] = $model->token;
            $data['expires_at'] = $model->expires_at;    
            $status = 200;
            $response = array(
                'status'    => 'SUCCESS',
                'message'   => "get access token.",
                'ref'     => 'access_token_successfully',
                'data'    => $data,
            );
        }
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }

    //To get user details
    public function mydetail($request)
    {
        $token = static::getAccessToken($request);
        $model = AccessTokens::where(['token' => $token])->first();
        $data = User::where(['id' => $model['user_id']])->first();
        $status = 200;
        $response = array(
            'status'    => 'SUCCESS',
            'message'   => "Login Successfully.",
            'ref'     => 'login_successfully',
            'data'    => $data,
        );
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }

    //To logout the user
    public function logout($request)
    {
        $token = static::getAccessToken($request);
        $model = AccessTokens::where(['token' => $token])->first();
        if ($model->delete()) {
            $response = [
                'status' => 1,
                'message' => "Logged Out Successfully"
            ];
            $status = 200;
            $response = array(
                'status'  => 'SUCCESS',
                'message' => "Logout Successfully.",
                'ref'     => 'logout_successfully',
                'data'    => [],
            );
        } 
        else 
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => "Invalid request",
                'ref'     => 'invalid_request',
            );
        }
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }

    //To create the new user 
    public function create($attributes)
    {
        $attributes['password'] = Hash::make($attributes['password']);
        $attributes['id'] = static::get_last_id('User','id');
        $model = User::create($attributes);
        $status = 200;
        $response = array(
            'status'  => 'SUCCESS',
            'message' => "User Created Successfully.",
            'ref'     => 'user_created_successfully',
            'data'    => []
        );
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }

    //To create the new primary id specific table 
    public static function get_last_id($collection_name, $field_name)
    {
        $collection = app("App\\$collection_name");
        $records = $collection::all();
        if(count($records) > 0){
            $last_id = $collection ::orderBy($field_name, 'desc')->take(1)->get();
            $incremental_id = $last_id[0][$field_name] + 1;
        } else {
            $incremental_id =  1;
        }
        return $incremental_id;
    }

    //To create the authorization code
    public static function createAuthorizationCode($user_id)
    {
        $model = new AuthorizationCodes;
        $model->code = md5(uniqid());
        $model->expires_at = time() + (60 * 5);
        $model->user_id = $user_id;
        if (isset($_SERVER['HTTP_X_APPLICATION_ID']))
            $app_id = $_SERVER['HTTP_X_APPLICATION_ID'];
        else
            $app_id = null;
        $model->app_id = $app_id;
        $model->created_at = time();
        $model->updated_at = time();
        $model->id = static::get_last_id('AuthorizationCodes','id');
        $model->save();
        return ($model);
    }

    //To create the accesstoken
    public static function createAccesstoken($authorization_code)
    {
        $auth_code = AuthorizationCodes::where(['code' => $authorization_code])->first();
        $model = new AccessTokens();
        $model->token = md5(uniqid());
        $model->auth_code = $auth_code->code;
        $model->expires_at = time() + (60*60 *24 * 60); // 60 days
        //$model->expires_at=time()+(60 * 2);  // 2 minutes
        $model->user_id = $auth_code->user_id;
        $model->created_at = time();
        $model->updated_at = time();
        $model->id = static::get_last_id('AccessTokens','id');
        $model->save();
        return ($model);
    }  

    //To create the get the token
    public static function getAccessToken($request)
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

    //To nearest the plaza
    static public function getnearestplaza($requestData)
    {
        //0.5 in miles 
        $meters_per_mile =  0.5 * 1609.34;
        if(empty($requestData['limit']))
            $limit = 10;
        else
            $limit = (int) $requestData['limit'];
        
        $plazaData = Plaza::Project(['_id' => 0 , "id" => 1 , "name" => 1])->where('loc', 'near', [
                '$geometry' => [
                    'type' => 'Point',
                    'coordinates' => [floatval($requestData['longitude']), floatval($requestData['latitude'])],
                ],
                '$maxDistance' => 1 * 1609.34,
            ])->where('is_deleted','=',0)->where('is_active','=',1)->paginate($limit)->toArray();
        $data['plazaData'] = $plazaData['data']; 
        $data['pagination'] = array();
        
        // calculate next record
        if ($plazaData['current_page'] < $plazaData['last_page']) {
            $next = $plazaData['current_page'] + 1;
        } else {
            $next = null;
        }
        // calculate previous record
        if ($plazaData['current_page'] > 1) {
            $previous = $plazaData['current_page'] - 1;
        } else {
            $previous = 1;
        }
        
        $data['pagination']['next']     = /*$next*/$plazaData['current_page'] + 1;
        $data['pagination']['previous'] = $previous;
        $data['pagination']['current']  = $plazaData['current_page'];
        $data['pagination']['first']    = 1;
        $data['pagination']['perpage']  = $plazaData['per_page'];
        $data['pagination']['last']     = $plazaData['last_page'];
        $data['pagination']['to']       = $plazaData['to'];
        $data['pagination']['from']     = $plazaData['from'];
        $data['pagination']['total']    = ceil($plazaData['total']/$plazaData['per_page']);
        $data['pagination']['totalRecords']    = $plazaData['total'];
        $status = 200;
        $response = array(
            'status'    => 'SUCCESS',
            'message'   => trans('messages.get_nearest_plaza_success'),
            'ref'     => 'get_nearest_plaza_success',
            'data'    => $data,
        );
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }
}   
?>