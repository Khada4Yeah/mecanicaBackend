<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
            Vehiculo::with("cliente", "cliente.usuario")->get(),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->guardarActualizarVehiculo($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id_vehiculo)
    {
        $vehiculo = Vehiculo::with("cliente", "cliente.usuario")->find(
            $id_vehiculo,
        );

        return $vehiculo
            ? response()->json($vehiculo)
            : response()->json(
                ["status" => "error", "message" => "Vehículo no encontrado"],
                404,
            );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id_vehiculo)
    {
        return $this->guardarActualizarVehiculo($request, $id_vehiculo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehiculo $vehiculo)
    {
        // Implementar lógica de eliminación si es necesario
    }

    /**
     * Obtener los vehículos de un cliente
     */
    public function vehiculosCliente(int $id_cliente)
    {
        return response()->json(
            Vehiculo::where("id_cliente", $id_cliente)->get(),
        );
    }

    /**
     * Guardar o actualizar un vehículo.
     */
    private function guardarActualizarVehiculo(
        Request $request,
        int $id_vehiculo = null,
    ) {
        $params_array = $request->json()->all();

        if (empty($params_array)) {
            return response()->json(
                ["status" => "error", "message" => "Error al enviar los datos"],
                400,
            );
        }

        $params_array = array_map("trim", $params_array);
        $validar_datos = $this->validarDatosVehiculo(
            $params_array,
            $id_vehiculo,
        );

        if ($validar_datos->fails()) {
            return $this->respuestaErrorValidacion($validar_datos);
        }

        try {
            DB::beginTransaction();
            $vehiculo = $id_vehiculo
                ? Vehiculo::find($id_vehiculo)
                : new Vehiculo();

            if (!$vehiculo) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Vehículo no encontrado",
                    ],
                    404,
                );
            }

            $vehiculo->placa = mb_strtoupper($params_array["placa"]);
            $vehiculo->marca = mb_strtoupper($params_array["marca"]);
            $vehiculo->modelo = mb_strtoupper($params_array["modelo"]);
            $vehiculo->chasis = mb_strtoupper($params_array["chasis"]);
            $vehiculo->motor = mb_strtoupper($params_array["motor"]);
            $vehiculo->id_cliente = $params_array["id_cliente"];
            $vehiculo->save();

            DB::commit();

            $message = $id_vehiculo
                ? "Datos actualizados correctamente"
                : "Datos guardados correctamente";
            return response()->json(
                ["status" => "success", "message" => $message],
                $id_vehiculo ? 200 : 201,
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

    /**
     * Validar los datos del vehículo.
     */
    private function validarDatosVehiculo(
        array $params_array,
        $id_vehiculo = null,
    ) {
        $mensajes = [
            "placa.required" => "La placa es requerida",
            "placa.size" =>
                "La placa debe tener 8 caracteres, incluido el guión",
            "placa.unique" => "La placa ya está registrada",
            "marca.required" => "La marca es requerida",
            "modelo.required" => "El modelo es requerido",
            "chasis.required" => "El chasis es requerido",
            "chasis.unique" => "El chasis ya está registrado",
            "motor.required" => "El motor es requerido",
            "motor.unique" => "El motor ya está registrado",
            "id_cliente.required" => "El cliente es requerido",
            "id_cliente.exists" => "El cliente no existe",
        ];

        return Validator::make(
            $params_array,
            [
                "placa" => [
                    "required",
                    "size:8",
                    Rule::unique("vehiculos")->ignore(
                        $id_vehiculo,
                        "id_vehiculo",
                    ),
                ],
                "marca" => "required",
                "modelo" => "required",
                "chasis" => [
                    "required",
                    Rule::unique("vehiculos")->ignore(
                        $id_vehiculo,
                        "id_vehiculo",
                    ),
                ],
                "motor" => [
                    "required",
                    Rule::unique("vehiculos")->ignore(
                        $id_vehiculo,
                        "id_vehiculo",
                    ),
                ],
                "id_cliente" => "required|exists:clientes,id_cliente",
            ],
            $mensajes,
        );
    }

    /**
     * Respuesta de error de validación.
     */
    private function respuestaErrorValidacion($validar_datos)
    {
        return response()->json(
            [
                "status" => "error",
                "message" => "Error al validar los datos",
                "errors" => $validar_datos->errors(),
            ],
            400,
        );
    }
}
