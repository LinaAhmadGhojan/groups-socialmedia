<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileGroup extends Model
{
    use HasFactory;
    protected $fillable=[
        'id_group',
        'id_file'
    ];


    static public function fileInGroup($id)
    {
    return  \DB::table('__files')->leftjoin('file_groups','file_groups.id_file','=','__files.id')
    ->where('file_groups.id_group','=',$id)
    ->get();
       
    }
}
