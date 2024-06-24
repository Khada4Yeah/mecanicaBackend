<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ficha</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: sans-serif;
            margin: 0;
            padding: 1cm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            font-size: 11pt;
            padding: 3px;
            text-align: left;
        }

        .header-cell {
            position: relative;
            text-align: center;
            font-size: 18px;
        }

        .header-cell img {
            position: absolute;
            transform: translateY(-50%);
            padding: 0;
            margin: 0
        }

        .header-cell h1 {
            margin: 1;
            display: inline-block;
        }

        /* Estilo para la marca de agua */
        .watermark {
            position: absolute;
            top: 30%;
            left: 15%;
            transform: translate(-50%, -50%);
            opacity: 0.4;
            pointer-events: none;
            /* Evita la interacción con otros elementos */
            z-index: -1;
            /* Coloca la marca de agua detrás del contenido */
            width: 70%;
            height: 70%;
        }

        .watermark img {
            opacity: 0.5;
            /* Opacidad del 10% */
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="{{ public_path('images/logo_2.png') }}" alt="Marca de Agua">
    </div>
    <table>
        <thead>
            <tr>
                <th colspan="3" class="header-cell">
                    <img src="{{ public_path('images/logo_2.png') }}" alt="Logo" width="145" height="90">
                    <h1>Mécanica Automotríz Espinoza</h1>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" style="text-align: center; font-size: 20pt">
                    <b>FICHA DE MANTENIMIENTO DE VEHÍCULOS</b>
                </td>
            </tr>
            <tr>
                <td><b>Ficha N° {{ $datos_ficha['numero_ficha'] }}</b></td>
                <td colspan="2"><b>Fecha de mantenimiento:</b> {{ $datos_ficha['fecha'] }}</td>
            </tr>
            <tr>
                <td style="background-color: dimgrey; text-align: center; font-size: 16pt" colspan="3">
                    <b>DATOS DEL CLIENTE</b>
                </td>
            </tr>
            <tr>
                <td><b>Nombres:</b></td>
                <td colspan="2">
                    {{ $cliente['nombres'] . ' ' . $cliente['apellido_p'] . ' ' . $cliente['apellido_m'] }}</td>
            </tr>
            <tr>
                <td><b>Cédula:</b></td>
                <td colspan="2">{{ $cliente['cedula'] }}</td>
            </tr>
            <tr>
                <td><b>Contacto:</b></td>
                <td colspan="2">{{ $cliente['celular'] }}</td>
            </tr>
            <tr>
                <td><b>Correo:</b></td>
                <td colspan="2">{{ $cliente['correo_electronico'] }}</td>
            </tr>
            <tr>
                <td style="background-color: dimgrey; text-align: center; font-size: 16pt" colspan="3">
                    <b>DATOS DEL VEHÍCULO</b>
                </td>
            </tr>
            <tr>
                <td><b>Placa:</b></td>
                <td colspan="2">{{ $vehiculo['placa'] }}</td>
            </tr>
            <tr>
                <td><b>Marca:</b></td>
                <td colspan="2">{{ $vehiculo['marca'] }}</td>
            </tr>
            <tr>
                <td><b>Modelo:</b></td>
                <td colspan="2">{{ $vehiculo['modelo'] }}</td>
            </tr>

            <tr>
                <td style="background-color: dimgrey; text-align: center; font-size: 16pt" colspan="3">
                    <b>REPARACIONES REALIZADAS</b>
                </td>
            </tr>

            @foreach ($rp as $r)
                <tr>
                    <td>{{ $r['tipo_reparacion'] }}</td>
                    <td style="text-align: center">
                        @foreach ($reparaciones as $re)
                            @if ($r['id_reparacion'] === $re['id_reparacion'])
                                <b>X</b>
                            @endif
                        @endforeach

                    </td>

                    <td>
                        @foreach ($reparaciones as $re)
                            @if (
                                $r['id_reparacion'] === $re['id_reparacion'] &&
                                    ($re['id_reparacion'] === 4 ||
                                        $re['id_reparacion'] === 9 ||
                                        $re['id_reparacion'] === 13 ||
                                        $re['id_reparacion'] === 14 ||
                                        $re['id_reparacion'] === 15))
                                Kilometraje Actual:
                                {{ $re['pivot']['informacion_adicional']['kilometraje_actual'] . ' - Kilometraje Sig.:' . $re['pivot']['informacion_adicional']['kilometraje_actual'] }}
                            @elseif ($r['id_reparacion'] === $re['id_reparacion'] && $re['id_reparacion'] === 23)
                                @foreach ($re['pivot']['informacion_adicional']['ruedas'] as $rueda)
                                    {{ $rueda . ' ' }}
                                @endforeach
                            @endif
                        @endforeach
                    </td>

                </tr>
            @endforeach
            <tr>
                <td style="background-color: dimgrey; text-align: center; font-size: 16pt" colspan="3">
                    <b>OBSERVACIONES</b>
                </td>
            </tr>

            <tr>
                @if ($datos_ficha['otros'] != null)
                    <td colspan="3">{{ $datos_ficha['otros'] }}</td>
                @else
                    <td colspan="3">Sin obseraciones...</td>
                @endif
            </tr>


        </tbody>
    </table>

</body>

</html>
