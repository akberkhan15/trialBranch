<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Hash;

class User extends Eloquent implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens;
    protected $table="users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'username', 'email','password','name','id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
     static public function authorizeRules()
    {
        return [
            'username' => 'required',
            'password' => 'required',
        ];
    }
    static public function accessTokenRules()
    {
        return [
            'authorization_code' => 'required',
        ];
    }
    static public function rules($id=NULL)
    {
        return [
            'username' => 'required|unique:users,username,'.$id,
            'password' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
        ];
    }
    public static function authorize($attributes){

        $model=User::where(['username'=>$attributes['username']])->select(['id','username','password'])->first();
        if(!$model)
            return false;


        if(Hash::check($attributes['password'],$model->password)) {
            return $model;
            // Right password
        } else {
            // Wrong one
        }



        return false;
    }
}