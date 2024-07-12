<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://app-mecanica-espinoza-backend-34fc43ce9a1f.herokuapp.com/fonts/stylesheet.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            position: relative;
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

        .header-cell img {
            float: left;
        }

        .header-cell h1 {
            text-align: center;
            vertical-align: middle;
            line-height: 90px;
            margin: 0 2.45cm 0 0;
            font-size: 28pt;
        }

        .fila-subtitulo {
            background-color: dimgrey;
            text-align: center;
            font-size: 16pt;
            column-span: 3;
        }

        .bloque-linea {
            display: inline-block;
            width: 50%;
        }

        .no-border {
            border-top: none;
        }

        .watermark {
            position: absolute;
            margin: 0;
            padding: 0;
            opacity: 0.1;
            z-index: 1;
            width: 18cm;
            height: 20cm;
            top: 66mm;
            left: 2cm
        }

        .nueva-pagina {
            page-break-before: always;
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
                    <h1>Mecánica Automotríz Espinoza</h1>
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
                <td class="fila-subtitulo" colspan="3">
                    <b>DATOS DEL CLIENTE</b>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <b>Nombres:</b>
                    {{ $cliente['nombres'] . ' ' . $cliente['apellido_p'] . ' ' . $cliente['apellido_m'] }}
                </td>

            </tr>
            <tr>
                <td colspan="3">
                    <b>Cédula:</b>
                    <div style="display: inline-block; margin-left: 12px">{{ $cliente['cedula'] }}</div>
                </td>

            </tr>
            <tr>
                <td colspan="4"> <b>Contacto:</b>
                    <div style="display: inline-block">{{ $cliente['celular'] }}</div>

                </td>
            </tr>
            <tr>
                <td colspan="3"><b>Correo:</b>
                    <div style="display: inline-block; margin-left: 14px">{{ $cliente['correo_electronico'] }}</div>
                </td>
            </tr>
            <tr>
                <td class="fila-subtitulo" colspan="3">
                    <b>DATOS DEL VEHÍCULO</b>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="bloque-linea">
                        <b>Placa:</b>
                        <div style="display: inline-block; margin-left: 25px">{{ $vehiculo['placa'] }}</div>
                    </div>
                    <div style="display: inline-block">
                        <b>Chasis:</b>
                        {{ $vehiculo['chasis'] }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="bloque-linea">
                        <b>Marca:</b>
                        <div style="display: inline-block; margin-left: 20px">{{ $vehiculo['marca'] }}</div>
                    </div>
                    <div style="display: inline-block">
                        <b>Motor:</b>
                        <div style="display: inline-block; margin-left: 6px">
                            {{ $vehiculo['motor'] }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <b>Modelo:</b>
                    <div style="display: inline-block; margin-left: 12px">{{ $vehiculo['modelo'] }}</div>
                </td>
            </tr>


            <tr>
                <td class="fila-subtitulo" colspan="3">
                    <b>REPARACIONES REALIZADAS</b>
                </td>
            </tr>

            @if (count($reparaciones) !== 0)
                @foreach ($rp as $r)
                    <tr>
                        <td style="width: 55%">{{ $r['tipo_reparacion'] }}</td>
                        <td style="text-align: center; width:5%">
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
                                        Km. Actual:
                                        {{ $re['pivot']['informacion_adicional']['kilometraje_actual'] }} - Km.
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
            @endif

            <tr>
                <td class="fila-subtitulo no-border" colspan="3">
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
