<?php
namespace App\Traits;
use Illuminate\Http\Request;
use App\Models\User;
use JWTAuth;

trait CommonTrait
{
    // get last id in a collection
    public function get_last_id( Request $request,$collection_name, $field_name)
    {
        $collection = app("App\Models\\$collection_name");
        $records = $collection::all();
        if(count($records) > 0){
            $last_id = $collection ::orderBy($field_name, 'desc')->take(1)->get();
            $incremental_id = $last_id[0][$field_name] + 1;
        } else {
            $incremental_id =  1;
        }
        return $incremental_id;
    }
}