<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Library\Services\GeoSearchService;
use Validator;

class GeoSearchController extends CommonController
{
    public function __construct(array $data = [])
    {
        $this->_GeoSearchService = new GeoSearchService();
    }

    /**
     * @desc TO add the PLAZA
     * @param POST add plaza
     * @return Array(name,latitude,longitude)
    */
    public function addplaza(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:20',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
        $validator = Validator::make($request->all(),$rules);
        if (!$validator->fails())
        {
            $requestData = $request->all();
            $response = $this->_GeoSearchService->addplaza($requestData);
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

    /**
     * @desc TO edit the PLAZA
     * @param POST edit plaza
     * @return Array(plaza_id,name,latitude,longitude)
    */
    public function editplaza(Request $request)
    {
        $rules = [
            'plaza_id' => 'required|string',
            'name' => 'required|string|min:3|max:20',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
        $validator = Validator::make($request->all(),$rules);
        if (!$validator->fails())
        {
            $requestData = $request->all();
            $response = $this->_GeoSearchService->editplaza($requestData);
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

    /**
     * @desc TO delete the PLAZA
     * @param POST delete plaza
     * @return Array(plaza_id)
    */
    public function deleteplaza(Request $request)
    {

        $rules = [
            'plaza_id' => 'required|string',
        ];
        $validator = Validator::make($request->all(),$rules);
        if (!$validator->fails())
        {
            $requestData = $request->all();
            $response = $this->_GeoSearchService->deleteplaza($requestData);
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
        $validator = Validator::make($request->all(),$rules);
        if (!$validator->fails())
        {
            $requestData = $request->all();
            $response = $this->_GeoSearchService->getnearestplaza($requestData);
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
