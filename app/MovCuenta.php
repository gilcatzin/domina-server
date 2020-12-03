<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class MovCuenta extends Model 
{
    protected $table = 'ban_movbancos';

    protected $fillable = [
        'id','id_catbancos','id_catconceptos','mes','ejercicio','fechamovto','descripcion','importe','iva','cuentacontable','id_catusuarios_c','id_catusuarios_m', 'created_at', 'updated_at'
    ];
}