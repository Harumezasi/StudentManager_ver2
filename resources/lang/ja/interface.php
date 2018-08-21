<?php
/**
 * Created by PhpStorm.
 * User: Seungmin Lee
 * Date: 2018-06-25
 * Time: 오전 10:44
 */

return [
    // 공통: 회원 유형
    'student'               => '学生',
    'professor'             => '教授',

    // 공통 : 회원정보
    'id'                    => 'ID',
    'std_id'                => '学籍番号',
    'prof_id'               => '教授ID',
    'prof_list'             => '教授リスト',
    'name'                  => '名前',
    'password'              => 'パスワード',

    // 공통 : 강의관리
    'subject_list'          => '講義リスト',
    'score'                 => '成績',
    'sub_num'               => '講義コード',
    'execute_date'          => '実施日',
    'type'                  => 'タイプ',
    'perfect_score'         => '満点',
    'gained_score'          => '取得点数',
    'contents'              => '説明',
    'type_explain'          => '講義タイプはjapanese(日本語)やmajor(専攻)の中で1つにしてください。',
    'join_list'             => '受講リスト',
    'timetable'             => '時間表',
    'period'                => ':period時限',
    'classroom'             => '講義室',

    // 공통 : 기능
    'need_care_alert'       => 'アラム',
    'notice'                => 'お知らせ',
    'evaluation'            => '基準',
    'comment'               => 'コメント',
    'schedule'              => '日程',
    'data'                  => 'データ',
    'info'                  => '情報',
    'count'                 => ':count回',

    // 일자 단위
    'year'                  => '年度',
    'term'                  => '学期',

    // 요일
    'monday'                => '月曜日',
    'tuesday'               => '火曜日',
    'wednesday'             => '水曜日',
    'thursday'              => '木曜日',
    'friday'                => '金曜日',
    'saturday'              => '土曜日',
    'sunday'                => '日曜日',

    // 학기
    '1st_term'              => '1学期',
    'summer_vacation'       => '夏休み',
    '2nd_term'              => '2学期',
    'winter_vacation'       => '冬休み',

    // 일정 포맷 설정
    'Y-m'                   => ':year年:month月',
    'Y-t'                   => ':year年:term',
    'Y-m-d'                 => ':year年:month月:day日',
    'Y-m-w'                 => ':year年:month月:week週目',

    // 지도반
    'class'                 => 'クラス',
    'class_time'            => 'クラスの登下校時間',
    'class_list'            => 'クラスのリスト',
    'class_id'              => 'クラスのコード',
    'class_name'            => 'クラスの名前',
    'tutor_id'              => '指導教授ID',
    'tutor_name'            => '指導教授の名前',
];