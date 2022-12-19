<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use SoftDeletes;

class GroupUser extends Model
{
    use HasFactory;
    protected $fillable=[
        'id_group',
        'id_user'
    ];



    public function notUserInGroup($id_group)
    {

    }
}
