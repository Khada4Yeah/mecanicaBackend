<?php

namespace App\Http\Controllers;

use App\Models\Ficha;
use App\Models\FichaReparacion;
use App\Models\Reparacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

use Carbon\Carbon;

class FichaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fichas = Ficha::with("vehiculo")->get();
        return response()->json($fichas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ficha_request = $request->input("ficha", []);
        $reparaciones_request = $request->input("reparaciones", []);

        if (
            !empty($ficha_request) &&
            (!empty($reparaciones_request) || $ficha_request["otros"] !== null)
        ) {
            // LIMPIAR DATOS
            $ficha_request = array_map("trim", $ficha_request);
            //$reparaciones_request = array_map("trim", $reparaciones_request);

            $mensajes = [
                "id_vehiculo.required" => "El vehículo es requerido",
                "id_vehiculo.exists" => "El vehículo no existe",
            ];

            // VALIDAR DATOS DE LA FICHA
            $validar_ficha = Validator::make(
                $ficha_request,
                [
                    "id_vehiculo" => "required|exists:vehiculos,id_vehiculo",
                ],
                $mensajes,
            );

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
                $ficha->fecha = empty($ficha_request["fecha"])
                    ? date("Y-m-d")
                    : $this->formatearFecha($ficha_request["fecha"]);
                $ficha->otros = $ficha_request["otros"] ?? null;
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
        $ficha = Ficha::with("vehiculo", "reparaciones")
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
     * Consultar las fichas de un un vehículo de un cliente.
     * @param int $id_vehiculo
     * @return \Illuminate\Http\Response
     */
    public function fichasClienteVehiculo(int $id_vehiculo)
    {
        $fichas = Ficha::with("vehiculo.cliente.usuario")
            ->where("id_vehiculo", $id_vehiculo)
            ->get();

        $fichas = $fichas->map(function ($ficha) {
            $vehiculo = $ficha->vehiculo;
            $cliente = $vehiculo->cliente;
            $usuario = $cliente->usuario;

            // Eliminar el cliente de la relación del vehículo
            unset($vehiculo->cliente);

            return [
                "id_ficha" => $ficha->id_ficha,
                "numero_ficha" => $ficha->numero_ficha,
                "fecha" => $ficha->fecha,
                "otros" => $ficha->otros,
                "id_vehiculo" => $ficha->id_vehiculo,
                "vehiculo" => $vehiculo,
                "cliente" => [
                    "id_cliente" => $cliente->id_cliente,
                    "id_usuario" => $cliente->id_usuario,
                    "usuario" => $usuario,
                ],
            ];
        });

        return response()->json($fichas, 200);
    }

    /**
     * Generar ficha de reparación.
     * @param string $id_ficha
     * @return \Illuminate\Http\Response
     */
    public function generarPdfFicha(string $id_ficha)
    {
        $datos_ficha = Ficha::with("vehiculo.cliente.usuario", "reparaciones")
            ->where("id_ficha", $id_ficha)
            ->first();

        $datos_ficha = $datos_ficha->toArray();

        $cliente = $datos_ficha["vehiculo"]["cliente"]["usuario"];
        $vehiculo = $datos_ficha["vehiculo"];
        $reparaciones = $datos_ficha["reparaciones"];
        $rp = Reparacion::all()->toArray();

        try {
            $pdf = PDF::setOptions([
                "enable-local-file-access" => true,
                "no-pdf-compression" => true,
                "margin-top" => "15mm",
                "margin-bottom" => "15mm",
                "margin-left" => "15mm",
                "margin-right" => "15mm",
            ])->loadView(
                "ficha",
                compact(
                    "datos_ficha",
                    "cliente",
                    "vehiculo",
                    "reparaciones",
                    "rp",
                ),
            );
            return $pdf->inline("invoice.pdf");
        } catch (\Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => "Error al generar el PDF",
                    "errors" => $e,
                ],
                400,
            );
        }
    }

    /**
     * Valida la información adicional de la reparación.
     */
    private function validateInformacionAdicional(
        $informacion_adicional,
        $tipo_reparacion,
    ) {
        switch ($tipo_reparacion) {
            // Casos que utilizan las mismas reglas de kilometraje
            case 4: // Cambio de bujías
            case 9: // Cambio de kit de banda de distribución
            case 13: // Cambio de aceite de motor
            case 14: // Cambio de aceite de caja de cambios
            case 15: // Cambio de aceite del diferencial
                $rules = [
                    "kilometraje_actual" => "required|integer",
                    "kilometraje_siguiente" => "required|integer",
                ];
                break;

            // Caso específico con reglas diferentes
            case 21: // Cambio de rulimanes de rueda
                $rules = [
                    "ruedas" => "required|array",
                    "ruedas.*" => "in:DI, DD, TI, TD",
                ];
                break;
            case 22: // Cambio de pastillas de freno
            case 23: // Limpieza de mordazas de freno
            case 24: // Cambio de amortiguadores
                $rules = [
                    "zona" => "required|array",
                    "zona.*" => "in:FRENTE, POSTERIOR",
                ];
                break;
        }

        $mensajes = [
            "kilometraje_actual.required" =>
                "El kilometraje actual es requerido",
            "kilometraje_actual.integer" =>
                "El kilometraje actual debe ser un número entero",
            "kilometraje_siguiente.required" =>
                "El kilometraje siguiente es requerido",
            "kilometraje_siguiente.integer" =>
                "El kilometraje siguiente debe ser un número entero",
            "ruedas.required" => "Las ruedas son requeridas",
            "ruedas.*.in" =>
                "Las ruedas deben ser DI, DD, TI o TD (delantera izquierda, delantera derecha, trasera izquierda, trasera derecha)",
            "zona.required" => "La zona es requerida",
            "zona.*.in" => "La zona debe ser FRENTE o POSTERIOR",
        ];

        return Validator::make($informacion_adicional, $rules, $mensajes);
    }

    /**
     * Formatear fecha.
     *
     * @param string $fecha
     * @return string
     */
    private function formatearFecha(string $fecha)
    {
        // Crear un objeto Carbon a partir de la cadena de fecha
        $date = Carbon::parse($fecha);

        // Formatear la fecha en el formato yyyy-MM-dd
        $formattedDate = $date->format("Y-m-d");

        return $formattedDate;
    }
}
