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
    <h2>알림 추가</h2>
    <hr>
    <form action="{{ route('tutor.attendance.care.insert') }}" method="post">
        <span>
            <select name="days_unit" id="select_day_unit">
                <option value="7" selected>일주일</option>
                <option value="30">한 달</option>
            </select>
            동안&nbsp;
            <select name="ada_type" id="select_ada_type">
                <option value="lateness">지각</option>
                <option value="early_leave">조퇴</option>
                <option value="absence">결석</option>
            </select>
            을&nbsp;
            <select name="continuative_flag" id="select_con_flag">
                <option value="1">연속</option>
                <option value="0">누적</option>
            </select>
            <input type="number" name="count" id="input_count" min="1" max="999">
            회 이상 할 경우&nbsp;
            <select name="alert_std_flag" id="select_alert_flag">
                <option value="0">나</option>
                <option value="1">나와 학생</option>
            </select>
            에게 알림
            <input type="submit" value="저장">
        </span>
    </form>
    <br>
    <h2>알림 확인</h2>
    <hr>
    <div id="div_alert_list">

    </div>
    <script language="JavaScript">
        $(document).ready(function() {
            // 01. 최초 데이터 획득
            $.ajax('{{ route('tutor.attendance.care.select') }}')
                .done(function(response) {
                    if (response.status === true) {
                        let list = $('#div_alert_list');

                        if(response.message === null) {
                            list.text("조회된 알림이 없습니다.");
                        } else {
                            for(let alert of response.message) {
                                let div_alert = document.createElement('div');
                                let message = document.createElement('span');

                                message.innerText = `${alert.days_unit}일동안 ${alert.ada_type} ${alert.continuative_flag}를 ${alert.count}번 한 경우 `;
                                message.innerText += alert.alert_std_flag ? '나와 학생' : '나';
                                message.innerText += '에게 알림';

                                div_alert.append(message);

                                let deleteButton = document.createElement('input');
                                deleteButton.setAttribute('type', 'button');
                                deleteButton.setAttribute('value', '삭제');
                                deleteButton.addEventListener('click', function(event) {
                                    $.ajax('{{ route('tutor.attendance.care.delete') }}', {
                                        method: "post",
                                        data: {
                                            alert_id: `${alert.id}`
                                        }
                                    })
                                        .done(function(response) {
                                            if(response.status === true) {
                                                location.reload(true);
                                            } else {

                                            }
                                        })
                                });

                                div_alert.append(deleteButton);
                                list.append(div_alert);
                            }
                        }
                    } else {

                    }
                });
        })
    </script>
</body>
</html>