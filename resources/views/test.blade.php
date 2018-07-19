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
    <header>
        <h1>시간표 등록 ^_^</h1>
        <tr/>
    </header>
    <section>
        <form action="{{ route('admin.subject.timetable.import') }}" method="post" enctype="multipart/form-data">
            <label for="upload_file">엑셀 업로드</label>
            <input type="file" name="upload_file" id="upload_file">
            <input type="submit" value="전송">
        </form>
    </section>
    <footer>

    </footer>
</body>
</html>