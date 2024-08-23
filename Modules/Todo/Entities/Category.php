<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Category extends Model

{
    protected $guarded = [
        'id'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

   

}
    