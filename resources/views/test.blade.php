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
    <style type="text/css">
        .main {
            border:         1px solid black;
            width:          100%;
            height:         100%;
            display:        table;
        }

        .list {
            display:        table-cell;
            border:         1px solid black;
            width:          200px;
        }

        .student_list {
            float:          left;
        }

        .option_list {
            float:          right;
        }
    </style>
</head>
<body>
    <div class="main">
        <!-- 좌측 DIV -> 학생 목록 -->
        <div class="list student_list">
            <div>학생 목록</div>
            <!-- 학생 목록 출력 버튼 -->
            <div>
                <input type="radio" name="std_type" value="total" id="radio_stdType_total">
                <label for="radio_stdType_total">전체</label>
                <input type="radio" name="std_type" value="filter" id="radio_stdType_filter" checked>
                <label for="radio_stdType_filter">문제</label>
                <input type="radio" name="std_type" value="attention" id="radio_stdType_attention">
                <label for="radio_stdType_attention">사랑</label>
            </div>
            <!-- 학생 정렬 기준 -->
            <div>
                <div>정렬 기준</div>
                <div>
                    <input type="radio" name="std_order" value="id" id="radio_stdOrder_id" checked>
                    <label for="radio_stdOrder_id">학번</label>
                    <input type="radio" name="std_order" value="name" id="radio_stdOrder_name">
                    <label for="radio_stdOrder_name">이름</label>
                </div>
            </div>

            <!-- 학생 목록 -->
            <div id="std_list">

            </div>
        </div>

        <!-- 중앙 DIV -> 분석 화면 -->
        <div>

        </div>

        <!-- 우측 DIV -> 분석 조건 목록 -->
        <div class="list option_list">
            <div>
                분석 조건
            </div>

            <!-- 출석 -->
            <div>
                <div>
                    > 출석
                </div>
                <div id="option_ada_list">

                </div>
            </div>

            <!-- 학업 -->
            <div>
                <div>
                    > 학업
                </div>
                <div id="option_study_list">

                </div>
            </div>
        </div>
    </div>

    <!-- script -->
    <script language="JavaScript">
        $(document).ready(function() {
            // 01. 클래스 정의
            // 01-01. 학생 객체 정의
            /**
             * @param       stdId:          학번
             * @param       name:           이름
             * @param       photoUrl:       사진 URL
             * @param       atLevel:        관심도
             * @param       atReason:       관심 사유
             * @constructor
             */
            $.Student = function(stdId, name, photoUrl, atLevel, atReason) {
                this.stdId      = stdId;
                this.name       = name;
                this.photoUrl   = photoUrl;
                this.atLevel    = atLevel;
                this.atReason   = atReason;
            };

            // 학생 객체 프로토타입 정의
            $.Student.prototype = {
                /**
                 *  함수명:        printElement
                 *  설명:          학생 목록에 학생 객체를 출력
                 */
                printElement: function() {
                    // 01. 엘리멘트 호출
                    let stdListDiv  = $('#std_list');
                    let selfDiv     = $("<div>");


                    // 02. 엘리멘트 내부속성 정의

                },

                selectEvent: function() {

                }
            };

            // 01-02. 옵션 객체 정의
            $.Option = function() {

            };

            $.Option.prototype = {

            };

            // 02. 학생 목록 호출 함수 정의


            // 03. 학업 분석조건 호출 함수 정의

            // 04. 그래프 종류 정의
        });
    </script>
</body>
</html>