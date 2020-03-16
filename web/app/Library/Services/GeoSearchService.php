<?php
namespace App\Library\Services;

use DB;
use App\Models\Plaza as Plaza;
use App\Traits\CommonTrait as CommonTrait;
use App\Traits\ValidationTrait as ValidationTrait;

class GeoSearchService {
    use CommonTrait;
    use ValidationTrait;
    //To edit the plaza
    public function editplaza($requestData)
    {
        $custom_validate = $this->edit_plaza_validation($requestData);
        if($custom_validate['status'])
        {
            $location =  array(
                "type" => "Point",
                "coordinates" => [(float)$requestData['longitude'],(float)$requestData['latitude']]
            );
            /* Edit Plaza Start */
            $data = array(
                "name"       => $requestData['name'],
                "loc"        => $location,
                "is_active"  => 1,
                "is_deleted" => 0,
            );
            $editplaza = Plaza::updateRecord(['id' => (int) $requestData['plaza_id']],$data);
            if($editplaza)
            {
                $status = 200;
                $response = array(
                    'status'    => 'SUCCESS',
                    'message'   => trans('messages.edit_plaza_success'),
                    'ref'     => 'edit_plaza_success',
                    'data'    => [],
                );
            }
            else
            {
                $status = 500;
                $response = array(
                    'status'  => 'FAILED',
                    'message' => trans('messages.error_data_save'),
                    'ref'     => 'error_data_save',
                );
            }
            /* Edit Plaza End */ 
        }
        else
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => $custom_validate['message'],
                'ref'     => $custom_validate['ref'],
            );
        }
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }
    //To add the plaza
    public function addplaza($requestData)
    {
        $custom_validate = $this->add_plaza_validation($requestData);
        if($custom_validate['status'])
        {
            $location =  array(
                "type" => "Point",
                "coordinates" => [(float)$requestData['longitude'],(float)$requestData['latitude']]
            );
            /* Add Plaza Start */
            $data = array(
                "name"       => $requestData['name'],
                "loc"        => $location,
                "is_active"  => 1,
                "is_deleted" => 0,
            );
            
            $addplaza = Plaza::add($data,'Plaza','id');
            /* Add Plaza End */ 

            if ($addplaza) 
            {
                $status = 200;
                $response = array(
                    'status'    => 'SUCCESS',
                    'message'   => trans('messages.plaza_add_success'),
                    'ref'     => 'plaza_add_success',
                    'data'    => [],
                );
            } else {
                $status = 500;
                $response = array(
                    'status'  => 'FAILED',
                    'message' => trans('messages.error_data_save'),
                    'ref'     => 'error_data_save',
               );
            } 
        }
        else
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => $custom_validate['message'],
                'ref'     => $custom_validate['ref'],
            );
        }
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }
    //To delete the plaza
    public function deleteplaza($requestData)
    {
        $custom_validate = $this->delete_plaza_validation($requestData);

        if($custom_validate['status'])
        {
            /* Delete Plaza Start */
            $data = array(
                "is_deleted" => 1,
            );
            /* Delete Plaza End */ 

            $deleteplaza = Plaza::updateRecord(['id' => (int) $requestData['plaza_id']],$data);
            if($deleteplaza)
            {
                $status = 200;
                $response = array(
                    'status'    => 'SUCCESS',
                    'message'   => trans('messages.delete_plaza_success'),
                    'ref'     => 'delete_plaza_success',
                    'data'    => [],
                );
            }
            else
            {
                $status = 500;
                $response = array(
                    'status'  => 'FAILED',
                    'message' => trans('messages.error_data_save'),
                    'ref'     => 'error_data_save',
                );
            }
        }
        else
        {
            $status = 400;
            $response = array(
                'status'  => 'FAILED',
                'message' => $custom_validate['message'],
                'ref'     => $custom_validate['ref'],
            );
        }
        $response_data['status'] = $status;
        $response_data['response'] = $response;
        return $response_data;
    }
    //To nearest the plaza
    static public function getnearestplaza($requestData)
    {
        $meters_per_mile = PLAZA_MILES * 1609.34;
        if(empty($requestData['limit']))
            $limit = LIMIT;
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
