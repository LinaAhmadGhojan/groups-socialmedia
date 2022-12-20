<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=[
        'name'
        ,'id_admin',
        'number_user',
        'number_file_check_in'
    ];


    static public function foundUser($id)
    {
    return  \DB::table('users')->leftjoin('group_users','group_users.id_group','!=','users.id')
    ->where('group_users.id_group','=',$id)->distinct()
    ->get('users.*');

    }

    static public function myGroup($id)
    {
    return  \DB::table('groups')->leftjoin('group_users','group_users.id_group','=','groups.id')
    ->where('group_users.id_user','=',$id)->join('users','groups.id_admin','=','users.id')
    ->select('group_users.*','number_user','user_name as user_admin','id_group','email as email_admin','id_admin','groups.name as name_group')
    ->get();

    }

    static public function userInGroup($id,$id_group)
    {
    return  \DB::table('users')->leftjoin('group_users','group_users.id_user','=','users.id')
    ->where('group_users.id_group','=',$id_group)
    ->get();

    }
}
