<?php

namespace Modules\Todo\Entities;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $guarded = [
        'id'
    ];
    protected static function newFactory()
    {
        //return FolderFactory::new();
    }
}
