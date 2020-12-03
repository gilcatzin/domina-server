<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class CatConcepto extends Model 
{
    protected $table = 'ban_catconceptos';

    protected $fillable = [
        'id','concepto','tipomovto','tieneiva','cancelaiva','tienefactura','cuentacontable','id_catusuarios_c','id_catusuarios_m', 'created_at', 'updated_at'
    ];
}