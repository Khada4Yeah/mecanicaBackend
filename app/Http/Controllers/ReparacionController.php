<?php

namespace App\Http\Controllers;

use App\Models\Reparacion;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReparacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reparaciones = Reparacion::all();
        return response()->json($reparaciones, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // RECOGER LOS DATOS POR POST
        $json = $request->input("json", null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            // LIMPIAR DATOS
            $params_array = array_map("trim", $params_array);

            // VALIDAR DATOS
            $validar_datos = Validator::make($params_array, [
                "tipo_reparacion" => "required",
            ]);

            if ($validar_datos->fails()) {
                // LA VALIDACION HA FALLADO
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Error al validar los datos",
                        "error" => $validar_datos->errors(),
                    ],
                    400,
                );
            } else {
                // VALIDACION CORRECTA

                try {
                    // INICIAR TRANSACCION
                    DB::beginTransaction();

                    // CREAR LA REPARACION
                    $reparacion = new Reparacion();
                    $reparacion->tipo_reparacion = mb_strtoupper(
                        $params_array["tipo_reparacion"],
                    );

                    // GUARDAR LA REPARACION
                    $reparacion->save();

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
    public function show(Reparacion $reparacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reparacion $reparacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reparacion $reparacion)
    {
        //
    }
}
