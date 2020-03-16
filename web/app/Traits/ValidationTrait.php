<?php
namespace App\Traits;
use Illuminate\Http\Request;
use JWTAuth;
use Hash;
use App\Models\User as User;
use App\Models\Plaza as Plaza;

trait ValidationTrait
{
    public function delete_plaza_validation($requestData)
    {
        $validate = array(
            "status"   => true,
            "message"  => "",
            "ref"      => "",
        );
        
        $is_plaza_id_exists = Plaza::where(
            [
                "id"  => (int) $requestData['plaza_id'],
                "is_deleted" => 0,
            ]
        )->first();
        if(empty($is_plaza_id_exists))
        {
            $validate['status']  = false;
            $validate['message'] = trans('messages.error_invalid_plaza_id');
            $validate['ref']     = "error_invalid_plaza_id";
            return $validate;
        }
        return $validate;
    }
    public function edit_plaza_validation($requestData)
    {
        $validate = array(
            "status"   => true,
            "message"  => "",
            "ref"      => "",
        );
        $is_plaza_id_exists = Plaza::where(
            [
                "id"  => (int) $requestData['plaza_id'],
                "is_deleted" => 0,
            ]
        )->first();
        if(empty($is_plaza_id_exists))
        {
            $validate['status']  = false;
            $validate['message'] = trans('messages.error_invalid_plaza_id');
            $validate['ref']     = "error_invalid_plaza_id";
            return $validate;
        }
        return $validate;
    }
    public function add_plaza_validation($requestData)
    {
        $validate = array(
            "status"   => true,
            "message"  => "",
            "ref"      => "",
        );
        return $validate;
    }  
}