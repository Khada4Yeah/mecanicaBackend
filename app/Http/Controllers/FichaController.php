<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use App\Models\FichaReparacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FichaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ficha_request = json_decode($request->input("ficha", null), true);

        $reparaciones_request = json_decode(
            $request->input("reparaciones", null),
            true,
        );

        if (!empty($ficha_request) && !empty($reparaciones_request)) {
            // LIMPIAR DATOS
            $ficha_request = array_map("trim", $ficha_request);
            //$reparaciones_request = array_map("trim", $reparaciones_request);

            // VALIDAR DATOS DE LA FICHA
            $validar_ficha = Validator::make($ficha_request, [
                "id_cliente" => "required",
                "id_vehiculo" => "required",
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
                $params_array["informacion_adicional"] ?? [];
            $tipo_reparacion = $params_array["tipo_reparacion"] ?? null;
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
                $ficha->otros = $ficha_request["otros"];
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
                        $reparacion["tipo_reparacion"];
                    if ($reparacion["informacion_adicional"] !== null) {
                        $ficha_reparacion->informacion_adicional = json_encode(
                            $reparacion["informacion_adicional"],
                        );
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
    public function show(Ficha $ficha)
    {
        //
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
