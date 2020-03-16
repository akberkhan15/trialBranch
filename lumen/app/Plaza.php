<?php namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Plaza extends Eloquent
{
	//protected $connection = 'mongodb';
    protected $collection = 'plaza';
    static $model = 'plaza';
    protected $fillable=['id','name','loc','is_active','is_deleted'];
    static function details($id)
	{
		$data = static::get($id);
		if($data || true)
		{
			return $data;
		}
		else
		 	return false;
	}
}
