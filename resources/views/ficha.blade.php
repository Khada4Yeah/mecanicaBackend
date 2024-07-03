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

        /* centar verticalmente la imagen en la fila header y enviarla a la izquierda, el texto que esta a la izquierda debe estar centrado
        horizontalmente y verticalmente*/
        .header-cell {}

        .header-cell img {
            float: left;
        }

        .header-cell h1 {
            text-align: center;
            vertical-align: middle;
            line-height: 90px;
            margin: 0 2.45cm 0 0;
        }

        /* .header-text {
            font-size: 28pt;
            text-align: center;
            vertical-align: middle;
        } */

        .watermark {
            position: absolute;
            top: 30%;
            left: 15%;
            opacity: 0.1;
            width: 70%;
            height: auto;
            z-index: 1;
        }
    </style>
</head>

<body>

    <img src="{{ public_path('images/logo_2.png') }}" alt="Marca de Agua" class="watermark">

    <table>
        <thead>
            <tr>
                <th colspan="3" class="header-cell">
                    <img src="{{ public_path('images/logo_2.png') }}" alt="Logo" width="100" height="90">
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
                            @if ($r['id_reparacion'] === $re['id_reparacion'])
                                @if (in_array($re['id_reparacion'], [4, 9, 13, 14, 15]))
                                    Kilometraje Actual:
                                    {{ $re['pivot']['informacion_adicional']['kilometraje_actual'] }} - Kilometraje
                                    Sig.:
                                    {{ $re['pivot']['informacion_adicional']['kilometraje_siguiente'] }}
                                @elseif ($re['id_reparacion'] === 23)
                                    @foreach ($re['pivot']['informacion_adicional']['ruedas'] as $rueda)
                                        {{ $rueda . ' ' }}
                                    @endforeach
                                @elseif (in_array($re['id_reparacion'], [24, 25, 26]))
                                    @foreach ($re['pivot']['informacion_adicional']['zona'] as $zona)
                                        {{ $zona . ' ' }}
                                    @endforeach
                                @endif
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
                    <td colspan="3" style="white-space: pre-line;">{{ $datos_ficha['otros'] }}</td>
                @else
                    <td colspan="3">Sin observaciones...</td>
                @endif
            </tr>


        </tbody>
    </table>

</body>

</html>
