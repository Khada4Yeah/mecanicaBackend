<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use App\Models\FichaReparacion;
use App\Models\Reparacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class FichaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fichas = Ficha::with("cliente.usuario", "vehiculo")->get();
        return response()->json($fichas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ficha_request = $request->input("ficha", []);
        $reparaciones_request = $request->input("reparaciones", []);

        if (!empty($ficha_request) && !empty($reparaciones_request)) {
            // LIMPIAR DATOS
            $ficha_request = array_map("trim", $ficha_request);
            //$reparaciones_request = array_map("trim", $reparaciones_request);

            // VALIDAR DATOS DE LA FICHA
            $validar_ficha = Validator::make($ficha_request, [
                "id_cliente" => "required | integer",
                "id_vehiculo" => "required | integer",
            ]);

            if ($validar_ficha->fails()) {
                // LA VALIDACION DE LA FICHA HA FALLADO
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Error al validar los datos",
                        "errors" => $validar_ficha->errors(),
                    ],
                    400,
                );
            }

            // VALIDAR DATOS DE LAS REPARACIONES
            $informacion_adicional =
                $reparaciones_request["informacion_adicional"] ?? [];
            $tipo_reparacion = $reparaciones_request["id_reparacion"] ?? null;

            if ($tipo_reparacion) {
                $validar_info_adicional = $this->validateInformacionAdicional(
                    $informacion_adicional,
                    $tipo_reparacion,
                );
                if ($validar_info_adicional->fails()) {
                    return response()->json(
                        [
                            "status" => "error",
                            "message" => "Error en la información adicional",
                            "errors" => $validar_info_adicional->errors(),
                        ],
                        400,
                    );
                }
            }

            // VALIDACION CORRECTA
            try {
                // INICIAR TRANSACCION
                DB::beginTransaction();

                // CREAR LA FICHA
                $ficha = new Ficha();
                $ficha->numero_ficha = 0;
                $ficha->fecha = date("Y-m-d");
                $ficha->otros = $ficha_request["otros"] ?? null;
                $ficha->id_cliente = $ficha_request["id_cliente"];
                $ficha->id_vehiculo = $ficha_request["id_vehiculo"];

                // GUARDAR EL USUARIO
                $ficha->save();

                // OBTENER EL ID DE LA FICHA
                $id_ficha = $ficha->id_ficha;

                // CREAR LAS REPARACIONES
                foreach ($reparaciones_request as $reparacion) {
                    $ficha_reparacion = new FichaReparacion();
                    $ficha_reparacion->id_ficha = $id_ficha;
                    $ficha_reparacion->id_reparacion =
                        $reparacion["id_reparacion"];
                    if ($reparacion["informacion_adicional"] !== null) {
                        $ficha_reparacion->informacion_adicional =
                            $reparacion["informacion_adicional"];
                    } else {
                        $ficha_reparacion->informacion_adicional = null;
                    }
                    $ficha_reparacion->save();
                }

                DB::commit();

                return response()->json(
                    [
                        "status" => "success",
                        "message" => "Datos guardados correctamente",
                    ],
                    200,
                );
            } catch (\Throwable $th) {
                DB::rollBack();

                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Error al guardar los datos",
                        "errors" => $th,
                    ],
                    400,
                );
            }
        } else {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Error al enviar los datos",
                ],
                400,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $parametro)
    {
        //Consultar las fichas con el cliente, datos de usuario, vehiculo y las reparaciones
        $ficha = Ficha::with("cliente.usuario", "vehiculo", "reparaciones")
            ->where("id_ficha", $parametro)
            ->first();
        return response()->json($ficha, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ficha $ficha)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ficha $ficha)
    {
        //
    }

    /**
     * Consultar las fichas de un cliente.
     */
    public function fichasCliente(string $parametro)
    {
        // Realiza la búsqueda condicional
        $fichas = Ficha::with("cliente.usuario", "vehiculo")
            ->whereHas("cliente.usuario", function ($q) use ($parametro) {
                $q->where("cedula", $parametro);
            })
            ->orWhereHas("vehiculo", function ($q) use ($parametro) {
                $q->where("placa", $parametro);
            })
            ->orderBy("fecha", "desc")
            ->get();

        // Retorna los resultados como JSON
        return response()->json($fichas, 200);
    }

    /**
     * Generar ficha de reparación.
     */
    public function generarPdfFicha(string $id_ficha)
    {
        // return view("ficha", compact("id_ficha"));

        $datos_ficha = Ficha::with(
            "cliente.usuario",
            "vehiculo",
            "reparaciones",
        )
            ->where("id_ficha", $id_ficha)
            ->first();

        $datos_ficha = $datos_ficha->toArray();

        $cliente = $datos_ficha["cliente"]["usuario"];
        $vehiculo = $datos_ficha["vehiculo"];
        $reparaciones = $datos_ficha["reparaciones"];
        $rp = Reparacion::all()->toArray();

        // var_dump($reparaciones);
        // die();

        $pdf = PDF::loadView(
            "ficha",
            compact("datos_ficha", "cliente", "vehiculo", "reparaciones", "rp"),
        );
        $pdf->setOption("enable-local-file-access", true);
        return $pdf->inline("invoice.pdf");
    }

    /**
     * Valida la información adicional de la reparación.
     */
    private function validateInformacionAdicional(
        $informacion_adicional,
        $tipo_reparacion,
    ) {
        $rules = [];
        switch ($tipo_reparacion) {
            // Caso 1: Cambio de bujías
            case 4:
                $rules = [
                    "kilometraje_actual" => "required|integer",
                    "kilometraje_siguiente" => "required|integer",
                ];
                break;
            // Caso 2: Cambio de kit de banda de distribución
            case 9:
                $rules = [
                    "kilometraje_actual" => "required|integer",
                    "kilometraje_siguiente" => "required|integer",
                ];
                break;
            // Caso 3: Cambio de aceite de motor
            case 13:
                $rules = [
                    "kilometraje_actual" => "required|integer",
                    "kilometraje_siguiente" => "required|integer",
                ];
                break;
            // Caso 4: Cambio de aceite de caja de cambios
            case 14:
                $rules = [
                    "kilometraje_actual" => "required|integer",
                    "kilometraje_siguiente" => "required|integer",
                ];
                break;
            // Caso 5: Cambio de aceite del diferencial
            case 15:
                $rules = [
                    "kilometraje_actual" => "required|integer",
                    "kilometraje_siguiente" => "required|integer",
                ];
                break;
            // Caso 6: Cambio de rulimanes de rueda
            case 23:
                $rules = [
                    "ruedas" => "required|array",
                    "ruedas.*" => "in:DI, DD, TI, TD",
                ];
                break;
        }
        return Validator::make($informacion_adicional, $rules);
    }
}