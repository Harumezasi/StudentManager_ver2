<?php
/**
 * 페이지 설명: <홈 페이지> Vue.js 파일 접근용
 * User: Seungmin Lee
 * Date: 2018-04-23
 * Time: 오후 2:33
 */
?>
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>환영합니다.</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
</head>
<body>
    <div id="app">
    </div>
    <script src='{{ asset('js/app.js') }}'></script>
</body>
</html>

