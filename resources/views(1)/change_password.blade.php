<?php
    use App\User;
    use App\Exceptions\NotValidatedException;
/**
 * Created by PhpStorm.
 * User: Seungmin Lee
 * Date: 2018-07-17
 * Time: 오전 10:38
 */

    // 유효한 키가 아닐 경우 => 접근 거부
    if(!User::where('verify_key', $key)->exists()) {
        echo '유효한 키가 아니여...';
        exit();
    }
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
        <h1>비밀번호 재설정 ^_^</h1>
        <tr/>
    </header>
    <section>
        <form action="{{ route('home.password_change.active') }}" method="post">
            <input type="hidden" name="key" value='{{ $key }}'>
            <label for="password">비번</label>
            <input type="password" name="password" id="password">
            <br>

            <label for="password_check">비번 확인</label>
            <input type="password" name="password_check" id="password_check">
            <br>

            <input type="submit" value="변경">
        </form>
    </section>
    <footer>

    </footer>
</body>
</html>
