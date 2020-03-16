<?php

namespace App\Http\Controllers;
use Auth;
use App\User;
use App\AuthorizationCodes;
use App\AccessTokens;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Library\Services\UserService;


class UsersController extends Controller
{
	public function __construct(array $data = []) 
    {
        $this->_UserService = new UserService();
    }
    
    /**
     * @desc User Login with username and password
     * @param  POST DATA (username,password)
     * @return Array(authorization_code)
    */

   	public function auth(Request $request)
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];
        $validator = $this->getValidationFactory()->make($request->all(), $rules);
        if (!$validator->fails()) 
        {
        	$response = $this->_UserService->login($request);
            $status = $response['status'];
            $response = $response['response'];
        }
        else
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => $validator->messages()->first(),
                'ref'     => 'missing_parameters',
            );
        }
        return $this->response($response,$status);
    }

    /**
     * @desc User accesstoken Request 
     * @param  POST DATA (authorization_code)
     * @return Array(access_token)
    */

    public function accesstoken(Request $request)
    {
        $rules = [
            'authorization_code' => 'required'
        ];
        $validator = $this->getValidationFactory()->make($request->all(), $rules);
        if (!$validator->fails()) 
        {
            $response = $this->_UserService->accesstoken($request);
            $status = $response['status'];
            $response = $response['response'];
        }
        else
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => $validator->messages()->first(),
                'ref'     => 'missing_parameters',
            );
        }
        return $this->response($response,$status);
    }

    /**
     * @desc User Detail Request 
     * @param  GET DATA ()
     * @return Array(data)
    */

    public function mydetail(Request $request)
    {
        $response = $this->_UserService->mydetail($request);
        $status = $response['status'];
        $response = $response['response'];
        return $this->response($response,$status);
    }

    /**
     * @desc User Logout Request 
     * @param  GET DATA ()
     * @return Array()
    */

    public function logout(Request $request)
    {
        $response = $this->_UserService->logout($request);
        $status = $response['status'];
        $response = $response['response'];
        return $this->response($response,$status);
    }

    /**
     * @desc User create Request 
     * @param  POST DATA (username,password,email)
     * @return Array()
    */

    public function create(Request $request)
    {
        $rules = [
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'email' => 'required|email|unique:users,email'
        ];
        $validator = $this->getValidationFactory()->make($request->all(), $rules);
        if (!$validator->fails()) 
        {
            $attributes = $request->all();
            $response = $this->_UserService->create($attributes);
            $status = $response['status'];
            $response = $response['response'];
            return $this->response($response,$status);

        }
        else
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => $validator->messages()->first(),
                'ref'     => 'missing_parameters',
            );
        }
        return $this->response($response,$status);
    }
     /**
     * @desc TO GET THE NEAREST PLAZA
     * @param POST get the plaza
     * @return Array(getnearestplaza,pagination)
    */
    public function getnearestplaza(Request $request)
    {
        $rules = [
            'latitude' => 'required',
            'longitude' => 'required',
            'limit' => 'integer',
        ];
        $validator = $this->getValidationFactory()->make($request->all(), $rules);
        if (!$validator->fails())
        {
            $requestData = $request->all();
            $response = $this->_UserService->getnearestplaza($requestData);
            $status   = $response['status'];
            $response = $response['response'];
        } else {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => $validator->messages()->first(),
                'ref'     => 'missing_parameters',
            );
        }
        return $this->response($response,$status);
    }
}