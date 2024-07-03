<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        //
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
        $credenciales_request = $request->json()->all();

        if (!empty($credenciales_request)) {
            // LIMPIAR DATOS
            $credenciales_request = array_map("trim", $credenciales_request);

            // VALIDAR DATOS
            $mensajes = [
                "correo_electronico.required" =>
                    "El correo electrónico es requerido",
                "correo_electronico.email" =>
                    "El correo electrónico no es válido",
                "clave.required" => "La contraseña es requerida",
            ];

            $validar_credenciales = Validator::make(
                $credenciales_request,
                [
                    "correo_electronico" => "required|email",
                    "clave" => "required",
                ],
                $mensajes,
            );

            if ($validar_credenciales->fails()) {
                // LA VALIDACION DE LA FICHA HA FALLADO
                return response()->json(
                    [
                        "message" => "Error al validar los datos",
                        "errors" => $validar_credenciales->errors(),
                    ],
                    400,
                );
            }

            // VALIDACION CORRECTA

            // BUSCAR USUARIO
            $usuario = Usuario::where(
                "correo_electronico",
                $credenciales_request["correo_electronico"],
            )->first();

            if (!$usuario) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Credenciales incorrectas",
                    ],
                    401,
                );
            }

            $administrador = Administrador::where(
                "id_usuario",
                $usuario->id_usuario,
            )->first();

            if (!$administrador) {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Credenciales incorrectas",
                    ],
                    401,
                );
            }

            $clave = $administrador->encriptarClave(
                $credenciales_request["clave"],
            );

            if ($clave === $administrador->clave) {
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
                        [
                            "status" => "error",
                            "error" => "No se pudo crear el token",
                        ],
                        500,
                    );
                }
            } else {
                return response()->json(
                    [
                        "status" => "error",
                        "message" => "Credenciales incorrectas",
                    ],
                    401,
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
