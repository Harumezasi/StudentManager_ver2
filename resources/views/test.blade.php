<?php
/**
 * Created by PhpStorm.
 * User: Seungmin Lee
 * Date: 2018-05-03
 * Time: 오후 7:49
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
    <script language="JavaScript" src="http://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>
    <form action="{{ route('home.join') }}" method="post" enctype="multipart/form-data">
        <div>
            <img src="" id="img_photo"><br>
            <label for="image_photo">사진 등록</label>
            <input type="file" name="photo" id="image_photo" accept="image/*">
        </div>
        <div>
            <label for="select_type">유형</label>
            <select name="type" id="select_type">
                <option value="student" selected>학생</option>
                <option value="professor">교수</option>
            </select>
        </div>
        <div>
            <label for="text_id">아이디</label>
            <input type="text" name="id" id="text_id">
            <input type="hidden" name="id_check" id="hidden_id_check" value="0">
            <input type="button" value="확인" id="button_check_id">
        </div>
        <div>
            <label for="password">패스워드</label>
            <input type="password" name="password" id="password">
        </div>
        <div>
            <label for="password_check">패스워드 확인</label>
            <input type="password" name="password_check" id="password_check">
        </div>
        <div>
            <label for="email">이메일</label>
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="phone">전화번호</label>
            <input type="text" name="phone" id="text_phone">
        </div>
        <div>
            <label for="text_name">이름</label>
            <input type="text" name="name" id="text_name" readonly>
        </div>
        <div>
            <label for="text_office">연구실</label>
            <input type="text" name="office" id="text_office" disabled>
        </div>
        <input type="submit" value="회원가입">
    </form>
    <script language="JavaScript">

        $(document).ready(function() {
            // 01. 타입에 따른 체크 버튼의 역할 변경
            $('#select_type').change(function(e) {
                let hiddenIdCheck = $('#hidden_id_check');
                let inputOffice = $('#text_office');
                let inputName   = $('#text_name');

                switch($(this).val()) {
                    case 'student':
                        hiddenIdCheck.val(0);
                        inputOffice.attr('disabled', true);
                        inputName.attr('readonly', true);
                        break;
                    case 'professor':
                        hiddenIdCheck.val(0);
                        inputOffice.attr('disabled', false);
                        inputName.attr('readonly', false);
                        break;
                }
            });

            // 02. 아이디중복여부 확인 이벤트
            $('#button_check_id').click(function() {
                if($('#text_id').val().length <= 0) {
                    return;
                }

                $.ajax({
                    url:"{{ route('home.join.check') }}",
                    data: {
                        type: $('#select_type').val(),
                        id: $('#text_id').val()
                    }
                })
                    .done(function(response) {
                        if(response.status === true) {
                            $('#hidden_id_check').val(1);
                            $('#text_name').val(response.message);
                        } else {
                            alert(response.message);
                        }
                    })
                    .fail(function(error) {
                        console.log(error)
                    })
            });
        });
    </script>
</body>
</html>