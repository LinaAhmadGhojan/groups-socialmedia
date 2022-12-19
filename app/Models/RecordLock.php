<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordLock extends Model
{
    use HasFactory;
    protected $fillable=[
        'lockable_id',
        'lockable_type',
        'user_id',
        'locked_at',
        'upload_date',
        'update_date'
    ];

      /**
     * Polymorphic relationship. Name of the relationship should be
     * the same as the prefix for the *_id/*_type fields.
     */
    public function lockable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship to user.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
