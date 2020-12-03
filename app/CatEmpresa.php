<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CatEmpresa extends Model 
{
    protected $table = 'gen_catempresas';

    protected $fillable = [
        'id','rfc','curp','razonsocial','logotipo','nombrecorto','id_catregimenfiscal','iva', 'id_catusuarios_c','id_catusuarios_m', 'created_at', 'updated_at'
    ];
}