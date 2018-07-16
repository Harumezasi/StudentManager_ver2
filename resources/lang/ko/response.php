<?php
/**
 * Title: 응답 메시지 언어팩 <한국어>
 * User: Seungmin Lee
 * Date: 6/11/2018
 * Time: 10:53 AM
 */

return [
    // 공통: CRUD 메시지
    'insert_success'        => ':element 등록에 성공하였습니다.',
    'insert_failed'         => ':element 등록에 실패하였습니다.',
    'update_success'        => ':element 수정에 성공하였습니다.',
    'update_failed'         => ':element 수정에 실패하였습니다.',
    'delete_success'        => ':element 삭제에 성공하였습니다.',
    'delete_failed'         => ':element 삭제에 실패하였습니다.',

    // 공통: 에러
    'data_not_found'        => '데이터를 찾을 수 없습니다.',
    'wrong_format'          => '데이터 형식이 맞지 않습니다.',
    'not_authorized'        => '허가되지 않은 접근입니다.',
    'no_authority'          => "해당 :contents에 접근할 권한이 없습니다.",

    // 공통 : 계정 관련
    'sign_in_success'       => "로그인 성공!",
    'wrong_id_or_password'  => "아이디 또는 비밀번호가 틀렸습니다.",

    // 회원가입 관련
    'not_joined'                => "회원가입이 되지 않았습니다. 먼저 회원가입을 하세요.",
    'not_exists_student'        => "해당 학번은 존재하지 않습니다.",
    'already_joined_student'    => "해당 학번은 이미 회원가입되어 있습니다.",
    'usable_id'                 => '사용 가능한 아이디입니다.',
    'already_joined_id'         => '이미 사용중인 아이디입니다.',
    'join_success'              => "회원가입 완료했습니다.",
    'join_failed'               => "회원가입에 실패했습니다.",

    // 01. 출석 메시지
    // 등교
    'already_sign_in'           => "오늘은 이미 출석하셨습니다.",
    'sign_in_disable_time'      => "자정부터 출석시간 30분 이전까지는 등교할 수 없습니다.",
    'sign_in_error_etc'         => "출석 인증에 실패하였습니다.",

    // 하교
    'sign_out_error_no_sign_in' => '최근 하교 내역이 존재합니다. (:sign_out_time)',
    'sign_out_error_no_data'    => '등교 내역이 없습니다.',
    'sign_out_error_etc'        => "하교 인증에 실패하였습니다.",

    // 지각|결석|조퇴

    // 02. 학업 관련 메시지
    // 학생 관리
    'wrong_std_id'              => '잘못된 학번입니다.',
    'none_adas'                 => '조회된 출석기록이 없습니다.',

    // 강의 관리
    'none_subject'              => "해당 학기에 조회된 강의가 없습니다.",
    'none_student'              => "등록되지 않은 학생이 존재합니다.",
    'not_registered_student'    => "해당 학생은 이 강의의 수강생이 아닙니다.",
    'wrong_score'               => '형식에 맞지 않게 입력된 점수가 존재합니다.',
    'score_overflow'            => "입력한 성적이 만점을 초과합니다.",

    // 성적 관리

    // 분석
    'not_usable_graph_type'     => "지원하지 않는 그래프 유형입니다.",
    'not_usable_minor_class'    => "지원하지 않는 소분류입니다.",

    // 03. 시스템 관리 메시지

    // 일정
    'schedule_already_exists'   => "지정 기간 이내에 이미 일정이 존재합니다.",
    'start_must_before_end'     => '시작일은 종료일보다 과거여야 합니다.',
];