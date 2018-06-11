<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Schedule;
use Validator;

/**
 *  클래스명:               AdminController
 *  설명:                   관리자에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index():                          관리자의 메인 페이지를 출력
 */
class AdminController extends Controller
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

    // 회원 관리


    // 일정 관리
    // 공통일정 조회
    public function selectCommonSchedule() {

    }

    // 공통일정 추가
    public function insertCommonSchedule(Request $request) {
        // 01. 요청 유효성 검증
        //$validTypes = implode(',', Schedule::TYPE);
        $validator = Validator::make($request->all(), [
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after:start_date',
            'name'              => 'required|string|min:2',
            'holiday_flag'      => 'required|boolean',
            'sign_in_time'      => 'required_if:holiday_flag,0,false|date',
            'sign_out_time'     => 'required_if:holiday_flag,0,false|date|after:sign_in_time',
            //'contents'          => 'string'
        ]);

        //if
    }

    // 공통일정 수정
    public function updateCommonSchedule() {

    }

    // 공통일정 삭제
    public function deleteCommonSchedule() {

    }
}
