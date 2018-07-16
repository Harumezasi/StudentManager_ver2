<?php
/**
 * 페이지 내용: 메일 전송용 양식
 * User: Seungmin Lee
 * Date: 2018-07-16
 * Time: 오전 10:50
 */
?>
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>{{ $title }} {{ $user->name }}</h1>
    <hr/>
    <p>{{ $body }}</p>
    <hr/>
    <footer>Mail from {{ config('app.url') }}</footer>
</body>
</html>