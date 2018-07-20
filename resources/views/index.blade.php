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
    <title>{{ config()->get('app.name') }}</title>
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:400,500,700,400italic|Material+Icons">
    <link href='//fonts.googleapis.com/earlyaccess/notosanskannada.css' rel="stylesheet">
    <link href="https://fonts.googleapis.com/earlyaccess/mplus1p.css" rel="stylesheet" />
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link href="http://fonts.googleapis.com/earlyaccess/nanumgothiccoding.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Muli' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Work Sans' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css?family=Gothic+A1:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200|Quicksand:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Tajawal:200" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Muli:200" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Alegreya+Sans:100" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Alegreya+Sans:100|Montserrat:100" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:200i" rel="stylesheet">
    <link href="http://fonts.googleapis.com/earlyaccess/notosansjapanese.css" rel="stylesheet">
</head>
<body>
    <div id="main_div"></div>
    @if(session()->has('alert'))
        <script>alert('{{ session()->get('alert') }}')</script>
    @endif
    <script src='{{ asset('js/app.js') }}'></script>
</body>
</html>
