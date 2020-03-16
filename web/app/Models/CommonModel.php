<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Image;
use File;
use Input;
use Location;
use Storage;
use \Cache;
use \Carbon\Carbon as Carbon;
use DB;

abstract class CommonModel extends Eloquent
{
    public static function get($id)
    {
        $data = Cache::get(static::$model . '-' . $id);
        if (empty($data) || true) {
           
            $dataObject = static::where(['id' => $id])->get()->first();
            
            if ($dataObject) {
                Cache::forever(static::$model . '-' . $id, $dataObject->toArray());
                $data = $dataObject->toArray();
            } else {
                $data = false;
            }

        }
        // if (isset($data['created_at'])) {
        //     $userLocation               = static::userLocation();
        //     $data['created_timestamp']  = empty($data['created_at']) ? '' : Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at'], 'UTC')->setTimezone($userLocation->time_zone)->format('Y-m-d H:i:s');
        //     $data['modified_timestamp'] = empty($data['updated_at']) ? '' : Carbon::createFromFormat('Y-m-d H:i:s', $data['updated_at'], 'UTC')->setTimezone($userLocation->time_zone)->format('Y-m-d H:i:s');
        // }
        //unset($data['created_at']);
        //unset($data['updated_at']);

        return $data;
    }

    // public static function dateToUTC($localTime, $timezone = '')
    // {
    //     if ($timezone == '') {
    //         $userLocation = static::userLocation();
    //         $timezone     = $userLocation->time_zone;
    //     }
    //     $dateInLocal = new Carbon($localTime, $timezone);
       
    //     return $dateInLocal->tz('utc')->format('Y-m-d H:i:s');
    // }

    
    public static function getAll($search = array(), $limit = PAGINATOR_LIMIT,$details = false, $orderBy = false)
    {
        // DB::enableQueryLog();
        $data['data'] = [];
        $projections = ['id'];
    	$query = static::select(static::$model.'.id','id');
       
        $query->where($search);
        if ($orderBy) {
            $query = $query->orderBy($orderBy[0],$orderBy[1]);
            //$query = $query->orderByRaw($orderBy);
        }
        
        if($limit<1)
        {
            $limit = 999;
            // set the current page
            \Illuminate\Pagination\Paginator::currentPageResolver(function () {
                return 1;
            });
        } else {
             // set the current page
            \Illuminate\Pagination\Paginator::currentPageResolver(function () {
                return !empty(Input::get('page')) ? Input::get('page') : '1';
            });           
        }
        
        $paginate = $query->paginate($limit,$projections);
        
        foreach ($paginate as $key => $row) {
            $data['data'][] = static::details($row->id);           
        }
        $pageData = $paginate->toArray();
        
        $data['pagination'] = array();
        
        // calculate next record
        if ($pageData['current_page'] < $pageData['last_page']) {
            $next = $pageData['current_page'] + 1;
        } else {
            $next = null;
        }

        // calculate previous record
        if ($pageData['current_page'] > 1) {
            $previous = $pageData['current_page'] - 1;
        } else {
            $previous = 1;
        }
        
        $data['pagination']['next']     = /*$next*/$pageData['current_page'] + 1;
        $data['pagination']['previous'] = $previous;
        $data['pagination']['current']  = $pageData['current_page'];
        $data['pagination']['first']    = 1;
        $data['pagination']['perpage']  = $pageData['per_page'];
        $data['pagination']['last']     = $pageData['last_page'];
        $data['pagination']['to']       = $pageData['to'];
        $data['pagination']['from']     = $pageData['from'];
        $data['pagination']['total']    = ceil($pageData['total']/$pageData['per_page']);
        $data['pagination']['totalRecords']    = $pageData['total'];
       
        // return data and 200 response
        return $data;
    }

    public static function updateRecord($search = array(), $data = array())
    {
        $records = static::where($search)->get();

        foreach ($records as $index => $row) {
            if ($row->update($data)) {
                //Cache::forget(static::$model . '-' . $row->id);
            }
        }
        return true;
    }
    
    public static function get_last_id($collection_name, $field_name)
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

    
    
    public static function add($data,$collection_name, $field_name)
    {
        $last_collection_id = self::get_last_id($collection_name, $field_name);
        $data['id'] = (int) $last_collection_id;
        return static::create($data);
    }

    public static function getAllByJoins($joins=array(),$search=array(),$limit = PAGINATOR_LIMIT,$details = false,$sort = false,$projections =array())
    {  
        $allData = [];
        foreach($projections as $k => $v)
	   	{
	   		$coloumsForAgg[$v] = 1;	
        }
        
        if(!empty($sort))
        {
            if($sort[1] == "desc" || $sort[1] == "DESC")
                $order_by = -1;
            else
                $order_by = 1;
            
            $sort = [$sort[0] => $order_by];
        }
        else
        {
            $sort = ['id' => -1];
        }
        $q = [];
        foreach ($joins as $key => $join) 
        {
            $q[]['$lookup'] = array(
                    "from" => $join['collection'],
                    "localField" => $join['local'],
                    "foreignField" => $join['foreign'],
                    "as" => $join['as'],
            );
            $q[] = array(
                '$unwind' => "$".$join['as']
            );
        }
        $opt = $q;
        if(!empty($search)) 
            $opt[] = ['$match' => $search ];
        
        $opt[] = ['$count' => 'total_count' ];
        
        $result = self::raw(function ($collection) use($opt) {
            return $collection->aggregate($opt);
        })->toArray();
        $number = !empty($result) ?  $result[0]['total_count'] : 0;
        
        $show = intval($limit);
        $pageNumber = intval(Input::get('page')!='' ? Input::get('page') : 1);
        if ($pageNumber <= 0) {
			$pageNumber = 1;
		}
		
		$roundedTotal = $show > 0 ? $number / floatval($show) : $number;
		$totalPages = intval($roundedTotal);
		/**
		 * Increase total_pages if wasn't integer
		 */
		if ($totalPages != $roundedTotal) {
			$totalPages++;
		}
        
		//Fix next
		if ($pageNumber <= $totalPages) {
			$next = $pageNumber + 1;
		} else {
			$next = $totalPages;
		}
		
		if ($pageNumber > 1) {
			$before = $pageNumber - 1;
		} else {
			$before = 1;
		}

        $data['pagination'] = array();
        $data['pagination']['next']  = $next;
        $data['pagination']['previous'] = $before;
        $data['pagination']['current']  = $pageNumber;
        $data['pagination']['first']  = 1;
        $data['pagination']['perpage']  = $show;
        $data['pagination']['last']  = $totalPages;
        $data['pagination']['total']  = $totalPages;
        $data['pagination']['totalRecords']  = $number;
        

        $opt = json_decode(json_encode($q));
	  	if(!empty($search))
            $opt[] = ['$match' => $search ];
        
        $opt[] = ['$sort' => $sort ];
        $opt[] = ['$skip' => $show * ($pageNumber - 1) ];
        $opt[] = ['$limit' => $show ];
        
        if(!empty($projections))
			$opt[] = ['$project' => $coloumsForAgg ];     
        
        $all_data = self::raw(function ($collection) use($opt) {
            return $collection->aggregate($opt);
        })->toArray();
        $data['data'] = $all_data;
		return $data;
    }

    public static function uploadImage($path, $file, $width = 1020)
    {
        $filename = '';
        
        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $filename  = uniqid() . '.' . $extension;
            Storage::put($path . '/' . $filename, File::get($file));
            
            $image = Image::make(Storage::get($path . '/' . $filename))
                ->resize($width, null,
                    function ($constraint) {
                        $constraint->aspectRatio();
                    })
                ->stream();
        Storage::put($path . '/' . $filename, $image);
        }
        return $filename;
    }
}
