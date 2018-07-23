<?php
/**
 * Created by PhpStorm.
 * User: Seungmin Lee
 * Date: 2018-06-25
 * Time: 오전 10:44
 */

return [
    // 공통: 회원 유형
    'student'               => '학생',
    'professor'             => '교수',

    // 공통 : 회원정보
    'id'                    => 'ID',
    'std_id'                => '학번',
    'prof_id'               => '교수 ID',
    'prof_list'             => '교수 목록',
    'name'                  => '이름',
    'password'              => '비밀번호',

    // 공통 : 강의관리
    'subject_list'          => '강의 목록',
    'score'                 => '성적',
    'sub_num'               => '강의 코드',
    'execute_date'          => '실시일자',
    'type'                  => '유형',
    'perfect_score'         => '만점',
    'gained_score'          => '취득 점수',
    'contents'              => '설명',
    'type_explain'          => '강의 유형은 japanese(일본어), major(전공) 중 하나로 지정하시오.',
    'join_list'             => '수강 목록',
    'timetable'             => '시간표',
    'period'                => ':period교시',
    'classroom'             => '강의실',

    // 공통 : 기능
    'need_care_alert'       => '알람',
    'notice'                => '알림',
    'evaluation'            => '기준',
    'comment'               => '코멘트',
    'schedule'              => '일정',
    'data'                  => '데이터',
    'info'                  => '정보',
    'count'                 => ':count회',

    // 일자 단위
    'year'                  => '연도',
    'term'                  => '학기',

    // 요일
    'monday'                => '월요일',
    'tuesday'               => '화요일',
    'wednesday'             => '수요일',
    'thursday'              => '목요일',
    'friday'                => '금요일',
    'saturday'              => '토요일',
    'sunday'                => '일요일',

    // 학기
    '1st_term'              => '1학기',
    'summer_vacation'       => '여름방학',
    '2nd_term'              => '2학기',
    'winter_vacation'       => '겨울방학',

    // 일정 포맷 설정
    'Y-m'                   => ':year년 :month월',
    'Y-t'                   => ':year년 :term',
    'Y-m-d'                 => ':year년 :month월 :day일',
    'Y-m-w'                 => ':year년 :month월 :week주차',

    // 지도반
    'class'                 => '지도반',
    'class_time'            => '지도반 등하교시간',
    'class_list'            => '지도반 목록',
    'class_id'              => '지도반 코드',
    'class_name'            => '지도반 이름',
    'tutor_id'              => '지도교수 ID',
    'tutor_name'            => '지도교수 이름',
];