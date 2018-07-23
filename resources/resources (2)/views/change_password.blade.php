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
        echo("<script>alert('有効じゃないコードです。')</script>");
        echo("<script>location.href='/';</script>");
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
    <style>
      .formArea{
          width: 290px;
      }
      .labelAreas {
        width: 80px;
        float: left;
      }
      .inputAreas{
        width: 200px;
      }
      .submitBtn{
        width: 100%;
        height: 60px;
      }
    </style>
</head>
<body>

  <div>
    <header>
        <h1>パスワード 変更</h1>
        <tr/>
    </header>
    <section>
        <form action="{{ route('home.password_change.active') }}" method="post" class="formArea" id="pwResetForm">
            <input type="hidden" name="key" value='{{ $key }}'>

            <div class="labelAreas">
              <label for="password">PW</label>
            </div>
              <input type="password" class="inputAreas" name="password" id="password">

            <br>

            <div class="labelAreas">
              <label for="password_check">PW 確認</label>
            </div>
              <input type="password" class="inputAreas" name="password_check" id="password_check">

            <br>

            <input type="button" value="変更" onclick="submitStart()" class="submitBtn">
        </form>
    </section>
    <footer>

    </footer>
  </div>

  <script>

  function submitStart() {
    let pw = document.getElementById('password').value
    let pw_ck = document.getElementById('password_check').value

    if(pw.length <= 0 || pw_ck.length <= 0){
      alert("パスワードを確かに入力してください。")
    }else if(pw == pw_ck){
      document.getElementById('pwResetForm').submit();
    }else{
      alert("パスワードが間違っています。")
    }
  }

  </script>
</body>
</html>
