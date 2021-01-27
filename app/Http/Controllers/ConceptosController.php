<?php

namespace App\Http\Controllers;
use App\CatConcepto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConceptosController extends Controller
{
    public function busqueda(Request $request){
        $conceptoID = $request->get("conceptoid");
        $descripcion = $request->get("concepto");
        $valor = $request->get("desde");

        $desde=0;
        $hasta=10;
        if($valor>0){
            $desde+=$valor;
            $hasta+=$valor;
        }

        if($conceptoID > 0){
            $query = DB::table('ban_catconceptos')
            ->select(DB::raw("*, (CASE WHEN tipomovto = 0 THEN 'INGRESO' ELSE 'EGRESO' END) AS destipomovto"))
            ->where("id", $conceptoID)
            ->orderBy("concepto", "ASC")
            ->skip($desde)->take($hasta)
            ->get();

         }else{
            if($descripcion !=  ""){
                $query = DB::table('ban_catconceptos')
                ->select(DB::raw("*, (CASE WHEN tipomovto = 0 THEN 'INGRESO' ELSE 'EGRESO' END) AS destipomovto"))
                ->orWhere("concepto","like", '%'.$descripcion.'%')
                ->orderBy("tipomovto", "ASC")
                ->orderBy("concepto", "ASC")
                ->skip($desde)->take($hasta)
                ->get();
            }else{
                $query = DB::table('ban_catconceptos')
                ->select(DB::raw("*, (CASE WHEN tipomovto = 0 THEN 'INGRESO' ELSE 'EGRESO' END) AS destipomovto"))
                ->orderBy("tipomovto", "ASC")
                ->orderBy("concepto", "ASC")
                ->skip($desde)->take($hasta)
                ->get();
            }
         }

        
        
        return $this->crearRespuesta($query, 200);

    }
    public function index(){
        return 'desdeController';
    }

    public function guardar(Request $request) {
        
        CatConcepto::create($request->all());
        $ultimo=DB::getPdo()->lastInsertId();

        return $this->crearRespuesta('El elemento ha sido creado', 201);
    
    }
//
    public function actualizar(Request $request, $id) {
        $catConcepto = CatConcepto::find($id);
        if  (!is_null($catConcepto)){
            
                $concepto = $request->get('concepto');
                $tipomovto = $request->get('tipomovto');
                $tieneiva = $request->get('tieneiva');
                $cancelaiva = $request->get('cancelaiva');
                $cuentacontable = $request->get('cuentacontable');
                $tienefactura = $request->get('tienefactura');
                $id_catusuarios_m = $request->get('id_catusuarios_m');
                
                $catConcepto->concepto = $concepto;
                $catConcepto->tipomovto = $tipomovto;
                $catConcepto->tieneiva = $tieneiva;
                $catConcepto->cuentacontable = $cuentacontable;
                $catConcepto->cancelaiva = $cancelaiva; 
                $catConcepto->tienefactura = $tienefactura;          
                $catConcepto->id_catusuarios_m = $id_catusuarios_m;
                
                $catConcepto->save();

                return $this->crearRespuesta('El elemento ha sido modificado', 201);
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }

    public function borrar($id) {
        $catConcepto = CatConcepto::find($id);
        
        if ($catConcepto){
            
            $catConcepto->delete();
            return $this->crearRespuesta('El elemento ha sido eliminado', 201);
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }
    //
}