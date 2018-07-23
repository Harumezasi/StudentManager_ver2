<?php
/**
 * Title: 응답 메시지 언어팩 <한국어>
 * User: Seungmin Lee
 * Date: 6/11/2018
 * Time: 10:53 AM
 */

return [
    // 공통: CRUD 메시지
    'select_failed'         => '照会された:elementのデータがありません。',
    'insert_success'        => ':elementの登録に成功しました。',
    'insert_failed'         => ':elementの登録に失敗しました.',
    'update_success'        => ':elementの修正に成功しました。',
    'update_failed'         => ':elementの修正に失敗しました。',
    'delete_success'        => ':elementの削除に成功しました。',
    'delete_failed'         => ':elementの削除に失敗しました。',

    // 공통: 에러
    'data_not_found'        => 'データを探すことができません。',
    'wrong_format'          => 'データのフォームが合いません。',
    'not_authorized'        => 'アクセス権限がありません。',
    'no_authority'          => "その:contentsのアクセス権限がありません。",

    // 공통 : 계정 관련
    'sign_in_success'       => "ログイン成功！",
    'wrong_id_or_password'  => "IDあるいはパスワードが間違いました。",
    'generate_key_failed'   => '認証キーの登録に失敗しました。’',

    // 공통 : 메일 관련
    'wrong_mail_address'    => 'メールアドレスが間違っています。',
    "send_mail_failed"      => 'メールの転送に失敗しました。',
    "send_mail_success"     => 'メールを成功的に送りました。',

    // 회원가입 관련
    'not_joined'                => "新規登録がされていません。まず新規登録をしてください。",
    'not_exists_student'        => "その学籍番号はありません。",
    'already_joined_student'    => "その学籍番号はすでに登録されています。",
    'usable_id'                 => '使用できるIDです。',
    'already_joined_id'         => 'すでに使用されているIDです。',
    'join_success'              => "新規登録に成功しました。",
    'join_failed'               => "新規登録に失敗しました。",

    // 01. 출석 메시지
    // 등교
    'already_sign_in'           => "今日はもう出席しました。",
    'sign_in_disable_time'      => "午前12時から出席時間の30分前までは登校できません。",
    'sign_in_error_etc'         => "出席認証に失敗しました。",

    // 하교
    'sign_out_error_no_sign_in' => 'すでに下校履歴があります。(:sign_out_time)',
    'sign_out_error_no_data'    => '登校履歴がありません。',
    'sign_out_error_etc'        => "下校認証に失敗しました。",

    // 지각|결석|조퇴

    // 02. 학업 관련 메시지
    // 학생 관리
    'wrong_std_id'              => '学籍番号が間違っています。',
    'none_adas'                 => '照会された出席履歴がありません。',

    // 강의 관리
    'none_subject'              => "その学期に照会された講義はありません。",
    'none_student'              => "登録されていない学生がいます。",
    'not_registered_student'    => "その学生はこの講義の受講生ではありません。",
    'wrong_score'               => 'フォームに合わないように入力された点数があります。',
    'score_overflow'            => "入力した点数が満点を超えます",
    'wrong_join'                => '学生:stdIdはこの講義が受講できません。',

    // 성적 관리

    // 분석
    'not_usable_graph_type'     => "支援していないグラフタイプです。",
    'not_usable_minor_class'    => "支援していない小分類です。",

    // 03. 시스템 관리 메시지

    // 일정
    'schedule_already_exists'   => "指定した期間の中にすでに日程があります。",
    'start_must_before_end'     => '開始日は終了日より前でなくてはいけません。',

    // 시간표 등록
    'timetable_wrong_subject'   => '講義コード:subject_idはその学期に開設された講義ではありません。',
    'timetable_is_not_unique'   => '1つの限で多数の講義があることはできません。',
    'timetable_wrong_class'     => 'ほかのクラスの講義があります。'
];