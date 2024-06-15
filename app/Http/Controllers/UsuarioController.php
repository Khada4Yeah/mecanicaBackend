<?php

namespace App\Http\Controllers;

use App\Models\Usuario;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
                "cedula" => "required",
                "nombres" => "required",
                "celular" => "required",
                "correo_electronico" => "required|email",
            ]);

            if ($validar_datos->fails()) {
                // LA VALIDACION HA FALLADO
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Error al validar los datos",
                        "errors" => $validar_datos->errors(),
                    ],
                    400,
                );
            } else {
                // VALIDACION CORRECTA

                try {
                    // INICIAR TRANSACCION
                    DB::beginTransaction();

                    // CREAR EL USUARIO
                    $usuario = new Usuario();
                    $usuario->cedula = $params_array["cedula"];
                    $usuario->nombres = mb_strtoupper($params_array["nombres"]);
                    $usuario->apellido_p = empty($params_array["apellido_p"])
                        ? null
                        : mb_strtoupper($params_array["apellido_p"]);
                    $usuario->apellido_m = empty($params_array["apellido_m"])
                        ? null
                        : mb_strtoupper($params_array["apellido_m"]);
                    $usuario->celular = $params_array["celular"];
                    $usuario->correo_electronico =
                        $params_array["correo_electronico"];

                    // GUARDAR EL USUARIO
                    $usuario->save();

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
    public function show(Usuario $usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Usuario $usuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Usuario $usuario)
    {
        //
    }
}
