<?php

namespace App\Http\Controllers;
use App\MovCuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovBancosController extends Controller
{
    public function busqueda(Request $request){
        $bancoID = $request->get("id_catbancos");
        $mes = $request->get("mes");
        $ejercicio = $request->get("ejercicio");
        $desde = $request->get("desde");
        $campos = "sb.saldoinicial";
        $array = array();

        //$fec = getdate();
        $fecha = date('Y-m-d',strtotime( $ejercicio."-".$mes."-01"));
        $fechainicial = date("d-m-Y",strtotime($fecha."- 1 days"));
        


        if ($mes > 1){
            for($i = 1; $i < $mes; $i++) {
                $campos = $campos."+ sb.ingreso".$i." - sb.egreso".$i;
            }
        }
        $campos = $campos." AS saldo";

        $SaldoInicial = DB::table('ban_catbancos AS b')
        ->join("ban_saldosbancos as sb","b.id", "=", "sb.id_catbancos")
        ->select(DB::raw("0 as id"), "b.id as id_catbancos", 
                 DB::raw("0 as mes"), DB::raw("0 as ejercicio"), DB::raw("now() as fechamovto"), DB::raw("0 as id_catconceptos"), DB::raw("'SALDO INICIAL' as descripcion"), DB::raw("'' as cuentacontable"), DB::raw("0 as iva"),
                 DB::raw("'' as concepto"), DB::raw("0 as tipomovto"), DB::raw("0 as tieneiva"), DB::raw("0 as tienefactura"),DB::raw("0 as cancelaiva"),
         DB::raw($campos), 
         DB::raw("0 AS ingreso"),
         DB::raw("0 AS egreso, 0 AS importe, 0 AS iva"))
        ->where("b.id", $bancoID)
        ->where("sb.ejercicio", $ejercicio)
        ->get();

        $query = DB::table('ban_movbancos AS m')
        ->join("ban_catbancos AS b","m.id_catbancos", "=", "b.id")
        ->join("sat_catbancos AS s","b.id_bancosat", "=", "s.id")
        ->join("ban_catconceptos as c","m.id_catconceptos", "=", "c.id")
        ->select("m.id", "m.id_catbancos", "m.mes", "m.ejercicio", "m.fechamovto", "m.id_catconceptos", "m.descripcion", "m.cuentacontable", "m.iva",
                 "c.concepto", "c.tipomovto", "c.tieneiva", "c.tienefactura","c.cancelaiva",
         DB::raw("0 AS saldo"),
         DB::raw("(CASE WHEN c.tipomovto = 0 THEN m.importe ELSE 0.00 END) AS ingreso"),
         DB::raw("(CASE WHEN c.tipomovto = 1 THEN m.importe ELSE 0.00 END) AS egreso"),
         DB::raw("m.importe, m.iva"))
        ->where("m.id_catbancos", $bancoID)
        ->where("m.mes", $mes)
        ->where("m.ejercicio", $ejercicio)
        ->orderBy("m.fechamovto", "ASC")
        ->get();


        // ** Crear el arreglo de la consulta
        $si = 0;
        $saldo = 0;
        foreach($SaldoInicial as $t){
            $saldo = $t->saldo;
            $si = $si + 1;
            $t->fechamovto = $fechainicial;
			$array[] = $t;

        }
        
        foreach($query as $t){
            
            $saldo = $saldo + $t->ingreso - $t->egreso;
            $t->saldo = $saldo;
			$array[] = $t;
        }
        if ($si == 0){
            $this->InsertarMovCuenta($bancoID,$ejercicio);
        }
        
        //  $data= json_decode($query, true);
        //  $si = 0;
        //  while( $row=($data)){
        //     //  if ($si ===0){
        //     //      $arr[] = $row;
        //     //      $arr[0]['id'] = 0;
        //     //      $arr[0]['concepto'] = "SALDO INICIAL";
                
        //     //      $si = 1;
        //     //  }
        //          $arr[] = $row;
        //  }
        // $query = json_encode($arr);
        return $this->crearRespuesta($array , 200);
        //return $this->$arr();

    }

    public function InsertarMovCuenta($cuentaid, $ejercicio){
        DB::insert('insert into ban_saldosbancos (id_catbancos,ejercicio,saldoinicial,ingreso1,ingreso2,ingreso3,ingreso4,ingreso5,ingreso6,ingreso7,ingreso8,ingreso9,ingreso10,ingreso11,ingreso12,egreso1,egreso2,egreso3,egreso4,egreso5,egreso6,egreso7,egreso8,egreso9,egreso10,egreso11,egreso12) values (?, ?, ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                     [$cuentaid, $ejercicio, 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]);
        $ultimo=DB::getPdo()->lastInsertId();
        return $this->crearRespuesta('Se Guardo el Ejercicio', 201);
    }

    public function AgregarSaldosMovCuenta($cuentaid, $ejercicio, $mes, $conceptoid, $importe){
        $query = DB::table('ban_catconceptos')
        ->select(DB::raw("*") )
        ->where("id", $conceptoid)
        ->orderBy("id", "ASC")
        ->get();

        $tipomovto = 0;
        foreach($query as $t){
            $tipomovto = $t->tipomovto;
        }
        if($tipomovto == 0){
            $saldo = "saldoinicial = saldoinicial + ".$importe;
            $campos = "ingreso".$mes." = ingreso".$mes." + ".$importe;
        }else{
            $saldo = "saldoinicial = saldoinicial - ".$importe;
            $campos = "egreso".$mes." = egreso".$mes." + ".$importe;
        }
        
        DB::update('update ban_saldosbancos set '.$campos.' where id_catbancos = ? and ejercicio = '.$ejercicio,
                    [$cuentaid, $ejercicio]);
        
        DB::update('update ban_saldosbancos set '.$saldo.' where id_catbancos = ? and ejercicio > '.$ejercicio,
                    [$cuentaid, $ejercicio]);

        
        return $this->crearRespuesta('Se Guardo el Ejercicio', 201);
    }

    public function QuitarSaldosMovCuenta($cuentaid, $ejercicio, $mes, $conceptoid, $importe){
        $query = DB::table('ban_catconceptos')
        ->select(DB::raw("*") )
        ->where("id", $conceptoid)
        ->orderBy("id", "ASC")
        ->get();

        $tipomovto = 0;
        foreach($query as $t){
            $tipomovto = $t->tipomovto;
        }
        if($tipomovto == 0){
            $saldo = "saldoinicial = saldoinicial - ".$importe;
            $campos = "ingreso".$mes." = ingreso".$mes." - ".$importe;
        }else{
            $saldo = "saldoinicial = saldoinicial + ".$importe;
            $campos = "egreso".$mes." = egreso".$mes." - ".$importe;
        }
        
        DB::update('update ban_saldosbancos set '.$campos.' where id_catbancos = ? and ejercicio = '.$ejercicio,
                    [$cuentaid, $ejercicio]);
        
        DB::update('update ban_saldosbancos set '.$saldo.' where id_catbancos = ? and ejercicio > '.$ejercicio,
                    [$cuentaid, $ejercicio]);

        
        return $this->crearRespuesta('Se actualizaron los saldos', 201);
    }

    public function index(){
        return 'desdeController';
    }
    public function guardarMovCuentas(Request $request) {
        //$this->validacion($request);
        $fec = $request->get('fechamovto');
        $fecha = date('Y-m-d',strtotime( $fec));
        $numMes = date("m", strtotime($fecha)); 
        $mesmovto = $request->get('mes');
        if ($numMes == $mesmovto){
            $id_catconceptos = $request->get('id_catconceptos');
            $id_catbancos = $request->get('id_catbancos');
            $ejercicio = $request->get('ejercicio');
            $importe = $request->get('importe');

            MovCuenta::create($request->all());
            $ultimo=DB::getPdo()->lastInsertId();

            $this->AgregarSaldosMovCuenta($id_catbancos, $ejercicio, $mesmovto, $id_catconceptos, $importe);
            //$this->fileUpload($request, $ultimo);
            return $this->crearRespuesta('El elemento ha sido creado', 201);
        }else{
            return $this->crearRespuestaError('La Fecha Capturada tiene diferente mes al Periodo', 300);
        }
    }
    //
    public function actualizarMovCuentas(Request $request, $id) {
        $movCuenta = MovCuenta::find($id);
        if  (!is_null($movCuenta)){
            // $this->validacion($request);
            $fec = $request->get('fechamovto');
            $fecha = date('Y-m-d',strtotime( $fec));
            $numMes = date("m", strtotime($fecha)); 
            $mesmovto = $request->get('mes');
            //return $this->crearRespuestaError("EL MES ES ".$numMes, 300);
            if ($numMes == $mesmovto){
                // Buscar el movimiento anterior
                $query = DB::table('ban_movbancos')
                ->select(DB::raw("*") )
                ->where("id", $id)
                ->orderBy("id", "ASC")
                ->get();
                
                foreach($query as $t){
                    $id_catconceptos = $t->id_catconceptos;
                    $id_catbancos = $t->id_catbancos;
                    $ejercicio = $t->ejercicio;
                    $importe = $t->importe;
                    $this->QuitarSaldosMovCuenta($id_catbancos, $ejercicio, $mesmovto, $id_catconceptos, $importe);
                }
                //****************************** */
                $id_catconceptos = $request->get('id_catconceptos');
                $id_catbancos = $request->get('id_catbancos');
                $ejercicio = $request->get('ejercicio');
                $descripcion = $request->get('descripcion');
                $cuentacontable = $request->get('cuentacontable');
                $importe = $request->get('importe');
                $iva = $request->get('iva');
                $id_catusuarios_m = $request->get('id_catusuarios_m');
                
                $movCuenta->fechamovto = $fecha;
                $movCuenta->id_catconceptos = $id_catconceptos;
                $movCuenta->descripcion = $descripcion;
                $movCuenta->cuentacontable = $cuentacontable;
                $movCuenta->importe = $importe; 
                $movCuenta->iva = $iva;          
                $movCuenta->id_catusuarios_m = $id_catusuarios_m;
                
                $movCuenta->save();

                $this->AgregarSaldosMovCuenta($id_catbancos, $ejercicio, $mesmovto, $id_catconceptos, $importe);

                return $this->crearRespuesta('El elemento ha sido modificado', 201);
            }else{
                return $this->crearRespuestaError('La Fecha Capturada tiene diferente mes al Periodo', 300);
            }
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }

    public function borrar($id) {
        $movCuenta = MovCuenta::find($id);
        
        if ($movCuenta){
            // Buscar el movimiento anterior
            $query = DB::table('ban_movbancos')
            ->select(DB::raw("*") )
            ->where("id", $id)
            ->orderBy("id", "ASC")
            ->get();
            
            foreach($query as $t){
                $id_catconceptos = $t->id_catconceptos;
                $id_catbancos = $t->id_catbancos;
                $ejercicio = $t->ejercicio;
                $importe = $t->importe;
                $mesmovto = $t->mes;
                $this->QuitarSaldosMovCuenta($id_catbancos, $ejercicio, $mesmovto, $id_catconceptos, $importe);
            }
            //****************************** */

            $movCuenta->delete();
            return $this->crearRespuesta('El elemento ha sido eliminado', 201);
        }
        return $this->crearRespuestaError('No existe el id', 300);
    }
}