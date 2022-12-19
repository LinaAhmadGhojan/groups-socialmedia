<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class _File extends Model
{
    use HasFactory;
    protected $fillable=[
        'id_owner',
        'name',
        'state',
        'public',
        'url',
        'upload_date',
        'update_date',
        'name_file'
    ];

    public function recordLock()
    {
        return $this->morphOne('App\Models\RecordLock', 'lockable');
    }

}
