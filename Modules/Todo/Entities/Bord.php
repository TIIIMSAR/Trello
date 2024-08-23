<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Bord extends Model
{
    protected $guarded = [
        'id'
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }


}
