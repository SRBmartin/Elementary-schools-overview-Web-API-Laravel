<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{url('img/favicon.png')}}"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ url('css/app.css') }}">
        <title>ФТН материјали</title>
    </head>
    <body>
        <div id="main" class="container">
            <h4>Семестар 3</h4>
            <div class="row">
                <div class="d-grid gap-2">
                    <button id="btn-3sms-fluidi" class="btn btn-primary butt" type="button">Системи за дистрибуцију и транспорт флуида</button>
                </div>
            </div>
            <div class="row">
                <div class="d-grid gap-2">
                    <button id="btn-3sms-namenski" class="btn btn-primary butt" type="button">Наменски рачунарски системи</button>
                </div>
            </div>
        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="{{ url('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
