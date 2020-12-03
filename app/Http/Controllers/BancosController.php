<?php

namespace App\Http\Controllers;
use App\CatBanco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BancosController extends Controller
{
    public function busqueda(Request $request){
        $usuarioID = $request->get("usuarioid");
        $bancoID = $request->get("bancoid");

        if($bancoID > 0){
            $query = DB::table('ban_catbancos as b')
            ->join("sat_catbancos AS s","s.id", "=", "b.id_bancosat")
            ->join("seg_catpermisos AS p","b.id", "=", "p.id_catbancos")
            ->join("gen_catempresas AS e","e.id", "=", "b.id_catempresas")
            ->select(DB::raw("b.*"), "s.c_banco, e.nombrecorto", 
            DB::raw("CONCAT(b.cuenta,'/',s.descripcion) AS banco"))
            ->where("p.id_catusuarios", $usuarioID)
            ->where("b.id", $bancoID)
            ->orderBy("s.descripcion", "ASC")
            ->orderBy("b.cuenta", "ASC")
            ->get();

         }else{
            $query = DB::table('ban_catbancos as b')
            ->join("sat_catbancos AS s","s.id", "=", "b.id_bancosat")
            ->join("seg_catpermisos AS p","b.id", "=", "p.id_catbancos")
            ->join("gen_catempresas AS e","e.id", "=", "b.id_catempresas")
            ->select(DB::raw("b.*,s.c_banco, e.nombrecorto"),
            DB::raw("CONCAT(b.cuenta,'/',s.descripcion) AS banco"))
            ->where("p.id_catusuarios", $usuarioID)
            ->orderBy("s.descripcion", "ASC")
            ->orderBy("b.cuenta", "ASC")
            ->get();
         }

        
        
        return $this->crearRespuesta($query, 200);

    }
    public function index(){
        return 'desdeController';
    }

    public function guardar(Request $request) {
        
            CatBanco::create($request->all());
            $ultimo=DB::getPdo()->lastInsertId();

            return $this->crearRespuesta('El elemento ha sido creado', 201);
        
    }
    //
    public function actualizar(Request $request, $id) {
        $CatBanco = CatBanco::find($id);
        if  (!is_null($CatBanco)){
            
                $cuenta = $request->get('cuenta');
                $tarjeta = $request->get('tarjeta');
                $clabe = $request->get('clabe');
                $id_bancosat = $request->get('id_bancosat');
                $cuentacontable = $request->get('cuentacontable');
                $contrato = $request->get('contrato');
                $id_catempresas = $request->get('id_catempresas');
                $id_catusuarios_m = $request->get('id_catusuarios_m');
                
                $CatBanco->cuenta = $cuenta;
                $CatBanco->tarjeta = $tarjeta;
                $CatBanco->clabe = $clabe;
                $CatBanco->cuentacontable = $cuentacontable;
                $CatBanco->contrato = $contrato; 
                $CatBanco->id_catempresas = $id_catempresas;          
                $CatBanco->id_catusuarios_m = $id_catusuarios_m;
                
                $CatBanco->save();

                return $this->crearRespuesta('El elemento ha sido modificado', 201);
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }

    public function borrar($id) {
        $CatBanco = CatBanco::find($id);
        
        if ($CatBanco){
            
            $CatBanco->delete();
            return $this->crearRespuesta('El elemento ha sido eliminado', 201);
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }
}