<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User as User;
use App\Models\UserIpAssigned as UserIpAssigned;
use App\Models\UserActivityLog as UserActivityLog;
use JWTAuth;
use Request;

class ProfileStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_data = JWTAuth::user()->toArray();
        $status = 402;
        if($user_data['is_deleted'])
        {
            $data = array(
                "program" => APPLICATION_NAME,
                "release" => API_VERSION,
                "code" =>  $status,
                "message" => trans('messages.error_delete_user'),
                "token" => "",
                "ref"   => "profile_deleted",
                "status" => "FAILED"
            );
            array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
            return \Response::json($data,$status);
        }
        if(!$user_data['is_active'])
        {
            $data = array(
                "program" => APPLICATION_NAME,
                "release" => API_VERSION,
                "code" =>  $status,
                "message" => trans('messages.error_inactived_user'),
                "token" => "",
                "ref"   => "profile_inactive",
                "status" => "FAILED"
            );
            array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
            return \Response::json($data,$status);
        }
        if($user_data['is_blocked'])
        {
            $data = array(
                "program" => APPLICATION_NAME,
                "release" => API_VERSION,
                "code" =>  $status,
                "message" => trans('messages.error_blocked_user'),
                "token" => "",
                "ref"   => "profile_blocked",
                "status" => "FAILED"
            );
            array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
            return \Response::json($data,$status);
        }
        if($user_data['is_ipprotocol'])
        {
            $ip_validation = false;
            $where = [];
            $where['is_active'] = 1;
            $where['is_deleted'] = 0;
            $where['user_id'] = (int) $user_data['id']; 
            $current_ip = getIp();
            $joins = [
                ['collection'=>'ippool','local'=>'ip_pool_id','foreign'=>'id','as' => 'ippool']
             ];
            $getuserip = $this->getAllByJoins($joins,$where,100000,0,false,['id','ippool.ip','user_id'])['data'];
            
            if(!empty($getuserip))
            {
                foreach($getuserip as $assign_user_ip)
                {
                    if($assign_user_ip['ippool']['ip'] == $current_ip)
                    {
                        $ip_validation = true;
                        break;
                    }
                }

                if(!$ip_validation)
                {
                    $block_user          = User::updateRecord(['id' => (int) $user_data['id']],['is_blocked' => 1]);

                    /* User Activity Start */
                    UserActivityLog::insertactivitylog('user','User Block due to ip.','user_ipblock');
                    /* User Activity End */

                    $data = array(
                        "program" => APPLICATION_NAME,
                        "release" => API_VERSION,
                        "code" =>  $status,
                        "message" => trans('messages.error_ipprotoctol_user'),
                        "token" => "",
                        "ref"   => "profile_ip_not_allowed",
                        "status" => "FAILED"
                    );
                    array_walk_recursive($data, function(&$item){if(is_numeric($item) || is_float($item)){$item=(string)$item;}});
                    return \Response::json($data,$status);
                }
            }
        }
        return $next($request);
    }

     /**
     * Handle an incoming request.
     *
     * this function is beacuse when we execute in common model its occur some problem all view pages 
     * @param  \Closure  $next
     * @return mixed
     */
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
        
        $opt = json_decode(json_encode($q));
	  	if(!empty($search))
            $opt[] = ['$match' => $search ];
        
        $opt[] = ['$sort' => $sort ];
        $opt[] = ['$limit' => $limit ];
        
        if(!empty($projections))
			$opt[] = ['$project' => $coloumsForAgg ];     
        
        $all_data = UserIpAssigned::raw(function ($collection) use($opt) {
            return $collection->aggregate($opt);
        })->toArray();
        $data['data'] = $all_data;
		return $data;
    }
}
