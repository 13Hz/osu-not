<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>osu!not - osu notification bot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d1b2a;
            color: #ffffff;
        }

        .container {
            max-width: 500px;
            margin-top: 10%;
            padding: 30px;
            text-align: center;
            background-color: #1b263b;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">OAuth Callback</h1>
    @if ($isSuccess)
        <div id="success-message" class="alert alert-success">
            <h4 class="alert-heading">Успех!</h4>
            <p>Успешная авторизация, можно закрыть окно</p>
        </div>
    @else
        <div id="error-message" class="alert alert-danger">
            <h4 class="alert-heading">Ошибка!</h4>
            <p>Что то пошло не так, пожалуйста повторите попытку</p>
        </div>
    @endif
</div>
</body>
</html>
