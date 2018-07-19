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
    <header>
        <h1>@lang('mail.usual_hello', ['user' => $user->name])</h1>
        <hr/>
    </header>
    <section>
        <p>@lang('mail.password_body')</p>
        <a href="{{ route('home.password_change.page', ['key' => $user->verify_key]) }}">@lang('mail.password_change')</a>
        <hr/>
    </section>
    <footer>
        <p>@lang('mail.usual_bye')</p>
        <a href="{{ config('app.url') }}"><img src="{{ config('app.url') }}/images/logo.png" alt="GRIT"></a>
    </footer>
</body>
</html>