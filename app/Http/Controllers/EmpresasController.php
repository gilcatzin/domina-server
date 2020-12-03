<?php

namespace App\Http\Controllers;
use App\CatEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresasController extends Controller
{
    public function busqueda(Request $request){
        $EmpresaID = $request->get("empresaid");

        if($EmpresaID > 0){
            $query = DB::table('gen_catempresas')
            ->select(DB::raw("*"))
            ->where("id", $EmpresaID)
            ->orderBy("nombrecorto", "ASC")
            ->get();

         }else{
            $query = DB::table('gen_catempresas')
            ->select(DB::raw("*"))
            ->orderBy("nombrecorto", "ASC")
            ->get();
         }

        
        
        return $this->crearRespuesta($query, 200);

    }
    public function index(){
        return 'desdeController';
    }

    public function guardar(Request $request) {
        
        CatEmpresa::create($request->all());
        $ultimo=DB::getPdo()->lastInsertId();

        return $this->crearRespuesta('El elemento ha sido creado', 201);
    
    }
//
    public function actualizar(Request $request, $id) {
        $CatEmpresa = CatEmpresa::find($id);
        if  (!is_null($CatEmpresa)){
            
                $nombrecorto = $request->get('nombrecorto');
                $razonsocial = $request->get('razonsocial');
                $rfc = $request->get('rfc');
                $curp = $request->get('curp');
                $logotipo = $request->get('logotipo');
                $id_catregimenfiscal = $request->get('id_catregimenfiscal');
                $iva = $request->get('id_catempresas');
                $id_catusuarios_m = $request->get('id_catusuarios_m');
                
                $CatEmpresa->nombrecorto = $nombrecorto;
                $CatEmpresa->razonsocial = $razonsocial;
                $CatEmpresa->rfc = $rfc;
                $CatEmpresa->curp = $curp;
                $CatEmpresa->logotipo = $logotipo; 
                $CatEmpresa->id_catregimenfiscal = $id_catregimenfiscal;          
                $CatEmpresa->id_catusuarios_m = $id_catusuarios_m;
                
                $CatEmpresa->save();

                return $this->crearRespuesta('El elemento ha sido modificado', 201);
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }

    public function borrar($id) {
        $CatEmpresa = CatEmpresa::find($id);
        
        if ($CatEmpresa){
            
            $CatEmpresa->delete();
            return $this->crearRespuesta('El elemento ha sido eliminado', 201);
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }
}