<!-- resources/views/test_image.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .watermark {
            position: absolute;
            margin: 0;
            padding: 0;
            width: 17cm;
            height: 19cm;
            top: 4cm;
            left: 1cm;
            /* background-image: url("file:///C:/Users/7w7YeaH7w7/Desktop/Programa Mecanica/mecanicaBackend/public/images/logo_2.png"); */
            background-image: url("{{ asset('images/logo_2.png') }}");
            background-size: 100%;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.1;
        }
    </style>
</head>

<body>
    <div class="watermark"></div>


</body>

</html>
