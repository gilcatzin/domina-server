<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SaldoBanco extends Model 
{
    protected $table = 'ban_saldosbancos';

    protected $fillable = [
        'id','id_catbancos','ejercicio','saldoinicial','ingreso1','ingreso2','ingreso3','ingreso4','ingreso5','ingreso6','ingreso7','ingreso8','ingreso9','ingreso10','ingreso11','ingreso12','egreso1','egreso2','egreso3','egreso4','egreso5','egreso6','egreso7','egreso8','egreso9','egreso10','egreso11','egreso12'
    ];
}