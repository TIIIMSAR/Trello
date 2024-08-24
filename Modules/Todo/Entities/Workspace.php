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

    public function boards()
    {
        return $this->hasMany(Bord::class);
    }

    public function categories()
    {
        return $this->hasManyThrough(Category::class, Bord::class, 'workspace_id', 'bord_id');
    }

    protected static function newFactory()
    {
        //return FolderFactory::new();
    }
}
