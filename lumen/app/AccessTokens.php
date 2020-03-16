<?php namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class AccessTokens extends Eloquent
{
	protected $table="accesstokens";
    protected $fillable = ['token','auth_code', 'expires_at','user_id','app_id','id'];
    static public function rules($id=NULL)
    {
        return [
            'user_id' => 'required',
            'token' => 'required|unique:access_tokens,token,'.$id,
            'auth_code' => 'required',
        ];
    }
}
?>