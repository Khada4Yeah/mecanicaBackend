<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Usuario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::with("usuario")->get();
        return response()->json($clientes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $params_array = $request->json()->all();
        $validar_datos = $this->validarDatosCliente($params_array);

        if ($validar_datos->fails()) {
            return $this->respuestaErrorValidacion($validar_datos);
        }

        try {
            DB::beginTransaction();
            $usuario = $this->crearUsuario($params_array);
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

    /**
     * Display the specified resource.
     */
    public function show(int $id_cliente)
    {
        $cliente = Cliente::with("usuario")->find($id_cliente);

        if (is_object($cliente)) {
            return response()->json($cliente);
        } else {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Cliente no encontrado",
                ],
                404,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id_usuario)
    {
        $params_array = $request->json()->all();
        $validar_datos = $this->validarDatosCliente($params_array, $id_usuario);

        if ($validar_datos->fails()) {
            return $this->respuestaErrorValidacion($validar_datos);
        }

        try {
            DB::beginTransaction();
            $usuario = $this->actualizarUsuario($params_array, $id_usuario);
            DB::commit();

            return response()->json(
                [
                    "status" => "success",
                    "message" => "Datos actualizados correctamente",
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        //
    }

    /**
     * Validar los datos del cliente.
     */
    private function validarDatosCliente(
        array $params_array,
        $id_usuario = null,
    ) {
        $mensajes = [
            "cedula.required" => "La cédula es requerida",
            "cedula.unique" => "La cédula ya está registrada",
            "cedula.size" => "La cédula debe tener 10 dígitos",
            "nombres.required" => "El nombre es requerido",
            "celular.required" => "El celular es requerido",
            "celular.size" => "El celular debe tener 10 dígitos",
            "correo_electronico.required" =>
                "El correo electrónico es requerido",
            "correo_electronico.email" => "El correo electrónico no es válido",
            "correo_electronico.unique" =>
                "El correo electrónico ya está registrado",
        ];

        // LIMPIAR DATOS
        $params_array = array_map("trim", $params_array);

        // VALIDAR DATOS
        return Validator::make(
            $params_array,
            [
                "cedula" => [
                    "required",
                    "size:10",
                    Rule::unique("usuarios")->ignore($id_usuario, "id_usuario"),
                ],
                "nombres" => "required|string",
                "celular" => "required|size:10",
                "apellido_p" => "nullable|string",
                "apellido_m" => "nullable|string",
                "correo_electronico" => [
                    "required",
                    "email",
                    Rule::unique("usuarios")->ignore($id_usuario, "id_usuario"),
                ],
            ],
            $mensajes,
        );
    }

    /**
     * Crear un nuevo usuario.
     */
    private function crearUsuario(array $params_array)
    {
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
        $usuario->correo_electronico = $params_array["correo_electronico"];
        $usuario->save();

        return $usuario;
    }

    /**
     * Actualizar un usuario existente.
     */
    private function actualizarUsuario(array $params_array, int $id_usuario)
    {
        $usuario = Usuario::where("id_usuario", $id_usuario)->first();

        if (!$usuario) {
            abort(404, "Usuario no encontrado");
        }

        $usuario->nombres = mb_strtoupper($params_array["nombres"]);
        $usuario->apellido_p = empty($params_array["apellido_p"])
            ? null
            : mb_strtoupper($params_array["apellido_p"]);
        $usuario->apellido_m = empty($params_array["apellido_m"])
            ? null
            : mb_strtoupper($params_array["apellido_m"]);
        $usuario->celular = $params_array["celular"];
        $usuario->correo_electronico = $params_array["correo_electronico"];
        $usuario->save();

        return $usuario;
    }

    /**
     * Respuesta de error de validación.
     */
    private function respuestaErrorValidacion($validar_datos)
    {
        return response()->json(
            [
                "message" => "Error al validar los datos",
                "errors" => $validar_datos->errors(),
            ],
            400,
        );
    }
}