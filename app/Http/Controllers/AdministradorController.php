<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Administrador;
use App\Models\Usuario;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AdministradorController extends Controller
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
        $request_data = $request->json()->all();

        DB::beginTransaction();

        try {
            $usuario = new Usuario();
            $usuario->cedula = $request_data["cedula"];
            $usuario->nombres = $request_data["nombres"];
            $usuario->apellido_p = $request_data["apellido_p"];
            $usuario->apellido_m = $request_data["apellido_m"];
            $usuario->correo_electronico = $request_data["correo_electronico"];
            $usuario->celular = $request_data["celular"];
            $usuario->save();

            $administrador = new Administrador();
            $administrador->id_usuario = $usuario->id_usuario;
            $administrador->clave = bcrypt($request_data["clave"]);
            $administrador->save();

            DB::commit();

            return response()->json(
                [
                    "status" => "success",
                    "message" => "Administrador creado correctamente",
                ],
                201,
            );
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(
                [
                    "status" => "error",
                    "message" => "Error al crear el administrador",
                    "error" => $th->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        $credentials = $request->validate(
            [
                "correo_electronico" => "required|email",
                "clave" => "required",
            ],
            [
                "correo_electronico.required" =>
                    "El correo electrónico es requerido",
                "correo_electronico.email" =>
                    "El correo electrónico no es válido",
                "clave.required" => "La contraseña es requerida",
            ],
        );

        $usuario = Usuario::where(
            "correo_electronico",
            $credentials["correo_electronico"],
        )->first();
        $administrador = $usuario
            ? Administrador::where("id_usuario", $usuario->id_usuario)->first()
            : null;

        if (
            !$usuario ||
            !$administrador ||
            !Hash::check($credentials["clave"], $administrador->clave)
        ) {
            return response()->json(
                ["status" => "error", "message" => "Credenciales incorrectas"],
                401,
            );
        }

        try {
            $token = JWTAuth::fromUser($usuario);
            return response()->json(
                [
                    "token" => $token,
                    "expires_in" => JWTAuth::factory()->getTTL() * 60,
                ],
                200,
            );
        } catch (\Throwable $th) {
            return response()->json(
                ["status" => "error", "error" => "No se pudo crear el token"],
                500,
            );
        }
    }

    public function obtenerUsuarioAutenticado()
    {
        try {
            if (!($user = JWTAuth::parseToken()->authenticate())) {
                return response()->json(
                    [
                        "status" => "error",
                        "error" => "Usuario no encontrado",
                    ],
                    404,
                );
            }
        } catch (TokenExpiredException $e) {
            return response()->json(
                [
                    "status" => "error",
                    "error" => "Token ha expirado",
                ],
                401,
            );
        } catch (TokenInvalidException $e) {
            return response()->json(
                [
                    "status" => "error",
                    "error" => "Token inválido",
                ],
                401,
            );
        } catch (JWTException $e) {
            return response()->json(
                [
                    "status" => "error",
                    "error" => "Error al procesar el token",
                ],
                500,
            );
        }

        return response()->json(compact("user"));
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);

        return response()->json(
            [
                "status" => "success",
                "message" => "Usuario deslogueado correctamente",
            ],
            200,
        );
    }
}
