<?php

namespace App\Http\Controllers;
use App\CatBancoSat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BancosSatController extends Controller
{
    public function busqueda(Request $request){
        $bancosatID = $request->get("bancosatid");

        if($bancosatID > 0){
            $query = DB::table('sat_catbancos')
            ->select(DB::raw("*"))
            ->where("id", $bancosatID)
            ->orderBy("descripcion", "ASC")
            ->get();

         }else{
            $query = DB::table('sat_catbancos')
            ->select(DB::raw("*"))
            ->orderBy("descripcion", "ASC")
            ->get();
         }

        
        
        return $this->crearRespuesta($query, 200);

    }
    public function index(){
        return 'desdeController';
    }
}