<?php namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CommonModel extends Eloquent
{
	public function get_last_id($collection_name, $field_name)
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
}
?>