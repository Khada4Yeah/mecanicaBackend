<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehiculos = Vehiculo::with("cliente", "cliente.usuario")->get();
        return response()->json($vehiculos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // RECOGER LOS DATOS POR POST
        $params_array = $request->json()->all();

        if (!empty($params_array)) {
            // LIMPIAR DATOS
            $params_array = array_map("trim", $params_array);

            $mensajes = [
                "placa.required" => "La placa es requerida",
                "placa.size" =>
                    "La placa debe tener 8 caracteres, incluido el guión",
                "placa.unique" => "La placa ya está registrada",
                "marca.required" => "La marca es requerida",
                "modelo.required" => "El modelo es requerido",
                "chasis.required" => "El chasis es requerido",
                "motor.required" => "El motor es requerido",
                "id_cliente.required" => "El cliente es requerido",
                "id_cliente.exists" => "El cliente no existe",
            ];

            // VALIDAR DATOS
            $validar_datos = Validator::make(
                $params_array,
                [
                    "placa" => "required|size:8",
                    "marca" => "required",
                    "modelo" => "required",
                    "chasis" => "required",
                    "motor" => "required",
                    "id_cliente" => "required|exists:clientes,id_cliente",
                ],
                $mensajes,
            );

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
                    $vehiculo = new Vehiculo();
                    $vehiculo->placa = mb_strtoupper($params_array["placa"]);
                    $vehiculo->marca = mb_strtoupper($params_array["marca"]);
                    $vehiculo->modelo = mb_strtoupper($params_array["modelo"]);
                    $vehiculo->chasis = mb_strtoupper($params_array["chasis"]);
                    $vehiculo->motor = mb_strtoupper($params_array["motor"]);
                    $vehiculo->id_cliente = $params_array["id_cliente"];

                    // GUARDAR EL USUARIO
                    $vehiculo->save();

                    DB::commit();

                    return response()->json(
                        [
                            "status" => "success",
                            "message" => "Datos guardados correctamente",
                        ],
                        201,
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
     * @param  int  $id_vehiculo
     * @return \Illuminate\Http\Response
     */
    public function show(int $id_vehiculo)
    {
        $vehiculo = Vehiculo::with("cliente", "cliente.usuario")->find(
            $id_vehiculo,
        );

        if (is_object($vehiculo)) {
            return response()->json($vehiculo);
        } else {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Vehículo no encontrado",
                ],
                404,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        //
    }

    /**
     * Obtener los vehiculos de un cliente
     */
    public function vehiculosCliente(int $id_cliente)
    {
        $vehiculos = Vehiculo::where("id_cliente", $id_cliente)->get();
        return response()->json($vehiculos);
    }
}
