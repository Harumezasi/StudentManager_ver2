<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 *  ## 테스트용
 */

Route::name('test.')->group(function() {
    // 세션 정보 호출
    Route::get('/session', [
        'as'    => 'session',
        'uses'  => 'HomeController@session',
    ]);

    // 요청 정보 확인
    Route::match(['GET', 'POST'], '/request', [
        'as'    => 'session',
        'uses'  => 'HomeController@request',
    ]);
});

/**
 *  01. 홈 컨트롤러 라우팅
 */
Route::name('home.')->group(function() {

    // 전체 메인 페이지
    Route::get('/', [
        'as'    => 'index',
        'uses'  => 'HomeController@index'
    ]);

    // 언어 설정
    Route::get('language/{locale}', [
        'as'    => 'language',
        'uses'  => 'HomeController@setLanguage'
    ]);

    // 회원가입 페이지
    Route::get('/join', [
        'as'    => 'join.main',
        'uses'  => 'HomeController@join'
    ]);

    // 회원가입 유형 획득
    Route::get('/join/{joinType}', [
        'as'    => 'join.form',
        'uses'  => 'HomeController@setJoinForm'
    ]);

    // 로그인 페이지
    Route::post('/login', [
        'as'    => 'login',
        'uses'  => 'HomeController@login'
    ]);

    // 로그아웃 기능
    Route::get('/logout', [
        'as'    => 'logout',
        'uses'  => 'HomeController@logout'
    ]);

    // 아이디/비밀번호 찾기 관련 기능 정의
    Route::get('/forgot', [
        'as'   => 'forgot',
        'uses' => 'HomeController@forgot'
    ]);
});



/**
 *  02. 학생 컨트롤러 기능
 */
Route::group([
    'as'        => 'student.',
    'prefix'    => 'student'
], function() {

    // 로그인 이후 사용 가능 기능
    Route::middleware(['check.student'])->group(function() {
        // 학생 메인 페이지
        Route::get('/main', [
            'as'    => 'index',
            'uses'  => 'StudentController@index'
        ]);

        // 내 정보



        // 출결 관리

        // 출결 정보 열람
        Route::get('/attendance', [
            'as'    => 'attendance',
            'uses'  => 'StudentController@getMyAttendanceRecords'
        ]);



        // 강의 관리

        // 내 수강정보 열람
        Route::get('/subject', [
            'as'    => 'subject',
            'uses'  => 'StudentController@getMySubjectList'
        ]);
    });
});



/**
 *  03. 교수 컨트롤러 기능
 */
Route::group([
    'as'        => 'professor.',
    'prefix'    => 'professor'
], function() {

    // 로그인 이후 사용 가능 기능
    Route::middleware(['check.professor'])->group(function() {
        // 교수 메인 페이지
        Route::get('/main', [
            'as'    => 'index',
            'uses'  => 'ProfessorController@index'
        ]);

        // 지도교수 여부 확인
        Route::post('/is_tutor', [
            'as'    => 'is_tutor',
            'uses'  => 'ProfessorController@isTutor'
        ]);



        // 내 정보



        // 강의 관리

        // 강의목록 조회
        Route::get('/subject/list', [
            'as'    => 'subject.list',
            'uses'  => 'ProfessorController@getMySubjectList'
        ]);

        // 해당 강의의 학생목록 조회
        Route::get('/subject/join_list', [
            'as'    => 'subject.join_list',
            'uses'  => 'ProfessorController@getJoinListOfSubject'
        ]);

        // 해당 과목에서 제출된 성적 목록 조회
        Route::get('/subject/scores', [
            'as'    => 'subject.scores',
            'uses'  => 'ProfessorController@getScoresList'
        ]);

        // 성적 등록 Excel 파일 생성
        Route::post('/subject/excel/download', [
            'as'    => 'subject.score.excel_download',
            'uses'  => 'ProfessorController@downloadScoreForm'
        ]);

        // Excel 파일을 이용한 성적 등록
        Route::post('/subject/excel/upload', [
            'as'    => 'subject.score.excel_upload',
            'uses'  => 'ProfessorController@uploadScoresAtExcel'
        ]);

        // 웹 인터페이스를 통한 직접 성적 등록
        Route::post('/subject/score/upload', [
            'as'    => 'subject.score.directly_upload',
            'uses'  => 'ProfessorController@uploadScores'
        ]);

        // 해당 성적 유형에서 학생들이 취득한 성적 확인
        Route::get('/subject/gained_scores', [
            'as'    => 'subject.score.gained_scores',
            'uses'  => 'ProfessorController@getGainedScoreList'
        ]);

        // 지정한 학생이 해당 과목에서 취득한 성적 목록 조회
        Route::get('/subject/details/scores', [
            'as'    => 'subject.details.scores',
            'uses'  => 'ProfessorController@detailScoresOfStudent'
        ]);



        // 내 지도반 관련 기능
        Route::middleware(['check.tutor'])->group(function() {

        });
    });
});


/**
 *  04. 관리자 컨트롤러 기능
 */
Route::group([
    'as'        => 'admin.',
    'prefix'    => 'admin'
], function() {

    // 로그인 이후 사용 가능 기능
    Route::middleware(['check.admin'])->group(function() {
        // 교수 메인 페이지
        Route::get('/main', [
            'as'    => 'index',
            'uses'  => 'AdminController@index'
        ]);
    });
});