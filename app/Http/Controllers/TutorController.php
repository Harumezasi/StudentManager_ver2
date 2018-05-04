<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use Illuminate\Http\Request;
use App\Professor;
use App\Student;
use App\StudyClass;
use Illuminate\Support\Carbon;


/**
 *  클래스명:               TutorController
 *  설명:                   지도교수에게 지원하는 기능을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 03일
 *
 *  메서드 목록
 *      - 메인
 *          = index:                            지도반 관리 기능의 메인 페이지에 진입
 *
 *
 *
 *      - 출결 관리
 *          = getAttendanceRecordsOfToday:      오늘의 출결 기록 조회
 *
 *
 *
 *      - 학생 관리
 */
class TutorController extends Controller
{
    // 01. 멤버 변수 선언

    // 02. 멤버 메서드 정의

    // 메인
    /**
     *  함수명:                         index
     *  함수 설명:                      지도반 관리 기능의 메인 페이지에 진입
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



    // 출결 관리
    // 오늘의 출결 기록 조회
    public function getAttendanceRecordsOfToday() {
        // 01. 데이터 획득
        $professor          = Professor::find(session()->get('user')->id);
        $myStudents         = $professor->studyClass->students()
                                ->join('users', 'users.id', 'students.id')
                                ->get(['students.id', 'users.name'])->all();
        $needCareAlerts     = $professor->needCareAlerts;
        $attendanceRecords  = [
            'sign_in'   => [],
            'sign_out'  => [],
            'lateness'  => [],
            'absence'   => [],
            'need_care' => []
        ];

        // 02. 학생별 현재 출결기록 획득
        // 조회일자 지정 (새벽 6시 이전이면 이전날 출석기록 조회)
        $searchTime = Carbon::create();
        if($searchTime->hour < 6) {
            $searchTime->subDay();
        }
        foreach($myStudents as $student) {
            // 출석 데이터 획득
            $attendance = $student->attendances()
                            ->start($searchTime->format('Y-m-d'))->end($searchTime->format('Y-m-d'))
                            ->get()->all();

            // ###### 조회된 출석 기록이 없다면 => 아직 학교에 안왔으므로 결석 ######
            if(sizeof($attendance) <= 0) {
                $attendanceRecords['absence'][] = $student;
                continue;
            }
            $attendance = $attendance[0];

            // 출결관리가 필요한 학생 필터링
            foreach($needCareAlerts as $alert) {
                switch($alert->notification_flag) {
                    case 'continuative_lateness':
                        break;
                    case 'continuative_leave':
                        break;
                    case 'continuative_absence':
                        break;
                    case 'total_lateness':
                        break;
                    case 'total_early_leave':
                        break;
                    case 'total_absence':
                        break;
                }
            }

            // 지각, 등교, 하교 필터링
            if($attendance->lateness_flag != 'good') {
                // 지각 => 등교 시각 첨부
                $student->sign_in_time = $attendance->sign_in_time;
                $attendanceRecords['lateness'][] = $student;
            } else if(!is_null($attendance->sign_out_time)) {
                // 하교 => 등교 시각, 하교 시각 첨부
                $student->sign_in_time  = $attendance->sign_in_time;
                $student->sign_out_time = $attendance->sign_out_time;
                $attendanceRecords['sign_out'][] = $student;
            } else {
                // 등교 => 등교 시각 첨부
                $student->sign_in_time = $attendance->sign_in_time;
                $attendanceRecords['sign_in'][] = $student;
            }
        }


        // 데이터 추출 결과값을 반환
        $data = [
            'sign_in'   => $attendanceRecords['sign_in'],
            'lateness'  => $attendanceRecords['lateness'],
            'absence'   => $attendanceRecords['absence'],
            'sign_out'  => $attendanceRecords['sign_out'],
            'need_care' => $attendanceRecords['need_care']
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    // 출결 필터링 조건 입력
    public function setNeedCareAlert(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
        ]);
    }



    // 학생 관리
    public function getMyStudentsList(Request $request) {
        // 01. 데이터 유효성 검사
        $validator = Validator::make($request->all(), [

        ]);

        if($validator->fails()) {
            throw new NotValidatedException($request->errors());
        }
    }
}
