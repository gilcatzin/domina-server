<?php

namespace App\Http\Controllers;
use App\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestamosController extends Controller
{
    public function busqueda(Request $request){
        $AlmacenID = $request->get("almacenid");
        $OperadorID = $request->get("operadorid");
        $desde = $request->get("desde");

        $query = DB::table('ps_encprestamos as e')
        ->join("ps_detprestamos as d","e.id", "=", "d.id_encprestamos")
        ->join("vw_catoperadores as o","e.id_catclientes", "=", "o.OperadorID")
        ->join("gen_catestatus as s","d.id_catestatus", "=", "s.EstatusID")
        ->join("ps_cattipoprestamos as t","e.id_cattipoprestamos", "=", "t.id")
        ->select("e.id", "e.coniva", "d.id AS id_detprestamos","d.numeropago", "d.fechapago",
         "d.capital", "d.saldocapital", "d.comision", "d.seguro", "d.intereses", "d.iva",  
         "d.saldocapital", "d.pagocapital", "d.pagointereses", "d.pagocomision", "d.pagomoratorio", "d.pagoiva", "d.pagoseguro", "d.id_catestatus", "s.Estatus", "d.id_movtimbrado","e.folio", "t.descripcion AS tipoprestamo", "o.Operador AS Cliente", "e.fecha", "e.fechainicio", "e.fechaprimerpago", "t.tasa", "e.frecuenciapago", "e.descripcion",
         DB::raw("(CASE WHEN s.Estatus = 'ACTIVO' THEN CASE WHEN t.mora > 0 THEN CASE WHEN  DATEDIFF(NOW(),d.fechapago) > 0 THEN ROUND((d.capital + d.comision + d.seguro) * ((t.mora/360)/100) * DATEDIFF(NOW(),d.fechapago),2) ELSE 0 END ELSE 0 END ELSE 0 END) AS moratorio"),
         DB::raw("(CASE WHEN s.Estatus = 'ACTIVO' THEN CASE WHEN DATEDIFF(NOW(),d.fechapago) > 0 THEN DATEDIFF(NOW(),d.fechapago) ELSE 0 END ELSE 0 END) AS diasatraso"))
        ->where("e.id_catalmacenes", $AlmacenID)
        ->orderBy("e.id", "DESC")
        ->orderBy("e.id_cattipoprestamos", "DESC")
        ->orderBy("e.id_catclientes", "DESC")
        ->orderBy("d.numeropago", "DESC")
        ->orderBy("d.fechapago", "ASC")
        ->skip($desde)
        ->take(6)
        ->get();
        return $this->crearRespuesta($query, 200);

    }
    public function index(){
        return 'desdeController';
    }

    //
}