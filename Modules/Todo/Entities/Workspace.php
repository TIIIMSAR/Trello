<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $guarded = [
        'id'
    ];

    
    public function likes()
    {
        return $this->belongsToMany(User::class, 'like');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    protected static function newFactory()
    {
        //return FolderFactory::new();
    }
}
