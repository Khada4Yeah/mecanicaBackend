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
    public function __construct()
    {
        $this->middleware("auth:api", ["except" => ["login"]]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Cliente::with("usuario")->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->guardarActualizarCliente($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id_cliente)
    {
        $cliente = Cliente::with("usuario")->find($id_cliente);

        return $cliente
            ? response()->json($cliente)
            : response()->json(
                ["status" => "error", "message" => "Cliente no encontrado"],
                404,
            );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id_usuario)
    {
        return $this->guardarActualizarCliente($request, $id_usuario);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        // Implementar lógica de eliminación si es necesario
    }

    /**
     * Guardar o actualizar un cliente.
     */
    private function guardarActualizarCliente(
        Request $request,
        int $id_usuario = null,
    ) {
        $params_array = $request->json()->all();

        if (empty($params_array)) {
            return response()->json(
                ["status" => "error", "message" => "Error al enviar los datos"],
                400,
            );
        }

        $params_array = array_map("trim", $params_array);
        $validar_datos = $this->validarDatosCliente($params_array, $id_usuario);

        if ($validar_datos->fails()) {
            return $this->respuestaErrorValidacion($validar_datos);
        }

        try {
            DB::beginTransaction();
            $usuario = $id_usuario ? Usuario::find($id_usuario) : new Usuario();

            if (!$usuario) {
                return response()->json(
                    ["status" => "error", "message" => "Usuario no encontrado"],
                    404,
                );
            }

            $this->llenarDatosUsuario($usuario, $params_array);
            $usuario->save();

            DB::commit();

            $message = $id_usuario
                ? "Datos actualizados correctamente"
                : "Datos guardados correctamente";
            return response()->json(
                ["status" => "success", "message" => $message],
                $id_usuario ? 200 : 201,
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
     * Llenar datos de usuario.
     */
    private function llenarDatosUsuario(Usuario $usuario, array $params_array)
    {
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
