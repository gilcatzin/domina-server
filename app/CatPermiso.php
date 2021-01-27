<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CatPermiso extends Model 
{
    protected $table = 'seg_catpermisos';

    protected $fillable = [
        'id','id_catbancos','id_catusuarios', 'created_at', 'updated_at'
    ];
}