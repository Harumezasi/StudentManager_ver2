<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 *  클래스명:               StudentController
 *  설명:                   학생 회원에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index():                          학생 회원의 메인 페이지를 출력
 *      - 내 정보 관리
 *      - 출결정보
 *      - 학업정보
 */
class StudentController extends Controller
{
    // 01. 멤버 변수 선언

    // 02. 멤버 메서드 정의

    // 메인
    /**
     *  함수명:                         index
     *  함수 설명:                      학생 회원의 메인 페이지를 출력
     *  만든날:                         2018년 4월 26일
     *
     *  매개변수 목록
     *  null
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return                         \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        return view('index');
    }

    // 출결 정보
    public function getMyAttendanceRecords() {

    }

    // 학업 정보
}
