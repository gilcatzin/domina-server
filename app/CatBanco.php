<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CatBanco extends Model 
{
    protected $table = 'ban_catbancos';

    protected $fillable = [
        'id','id_catempresas','id_bancosat','cuenta','tarjeta','clabe','contrato','cuentacontable','id_catusuarios_c','id_catusuarios_m', 'created_at', 'updated_at'
    ];
}