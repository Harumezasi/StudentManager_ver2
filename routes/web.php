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
    Route::middleware(['check.login'])->group(function() {
        // 학생 메인 페이지
        Route::get('/main', [
            'as'    => 'index',
            'uses'  => 'StudentController@index'
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
    Route::middleware(['check.login'])->group(function() {
        // 교수 메인 페이지
        Route::get('/main', [
            'as'    => 'index',
            'uses'  => 'ProfessorController@index'
        ]);

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
    Route::middleware(['check.login'])->group(function() {
        // 교수 메인 페이지
        Route::get('/main', [
            'as'    => 'index',
            'uses'  => 'AdminController@index'
        ]);

    });
});