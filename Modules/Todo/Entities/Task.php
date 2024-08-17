<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
     /**
     * The attributes that are mass assignable.
     */
    protected $guarded = [
        'id'
    ];


    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function getIsUserLikedAttribute()
    {
        return $this->likes()->where('user_id', $this->user()->id)->exists();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_tasks');
    }

    protected static function newFactory()
    {
        //return TaskFactory::new();
    }
}
