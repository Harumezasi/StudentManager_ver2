<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use Illuminate\Http\Request;
use Validator;
use App\Professor;

/**
 *  클래스명:               ProfessorController
 *  설명:                   교수 회원에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index():                          교수 회원의 메인 페이지를 출력
 *
 *
 *
 *      - 내 정보 관리
 *          = downloadScoreForm
 *
 *          = uploadScore
 *
 *      - 강의 관리
 *
 *      - 지도반 관리
 */
class ProfessorController extends Controller
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



    // 내 정보 관리



    // 강의 관리
    /**
     *  함수명:                        getMySubjectList
     *  함수 설명:                     내가 담당하는 강의 목록을 획득
     *  만든 날:                       2018년 4월 30일
     */
    public function getMySubjectList(Request $request) {
        // 01. 유효성 검증
        $validator = Validator::make($request->all(), [

        ]);
    }

    /**
     *  함수명:                         downloadScoreForm
     *  함수 설명:                      성적 등록을 위한 엑셀 양식을 다운로드
     *  만든날:                         2018년 4월 30일
     *
     *  매개변수 목록
     *  @param $request:                요청 객체
     *
     *  지역변수 목록
     *  $professor:                     성적을 등록하는 교수의 아이디
     *  $lecture:                       성적이 등록되는 과목 정보
     *  $fileName:                      파일 이름
     *  $scoreType:                     성적 유형
     *  $content:                       이 성적에 대한 설명
     *  $perfectScore:                  만점
     *  $fileType:                      파일 확장자
     *  $studentList:                   학생 목록
     *  $data:                          엑셀 파일에 등록할 데이터
     *
     *  반환값
     *  @return                         \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     *  예외
     *  @throws NotValidatedException
     */
    public function downloadScoreForm(Request $request) {
        // 01. 입력값 검증
        $validator = Validator::make($request->all(), [
            'file_name'         => 'required|string',
            'execute_date'      => 'required|date',
            'score_type'        => 'required|in:1,2,3,4',
            'content'           => 'required|string|min:2',
            'perfect_score'     => 'required|between:1,999',
            'file_type'         => 'required|string|in:xlsx,xls,csv',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor = Professor::findOrFail(session()->get('user')->id);
    }



    // 지도반 관리
}