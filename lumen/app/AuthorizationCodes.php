<?php namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class AuthorizationCodes extends Eloquent
{
    protected $table="code";
    protected $fillable = ['code', 'expires_at','user_id','app_id','id'];
    static public function rules($id=NULL)
    {
        return [
            'user_id' => 'required',
            'code' => 'required|unique:authorization_codes,code,'.$id,
        ];
    }

    public static function isValid($code)
    {
        $model=AuthorizationCodes::where(['code'=>$code])->first();

        if(!$model||$model->expires_at<time())
        {
            return(false);
        }
        else
            return($model);
    }
    
}
?>