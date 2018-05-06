<?php

namespace App\Http\Controllers;

use App\NeedCareAlert;
use Mockery\Exception;
use Validator;
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
                $attendanceStat = $student->selectAttendancesStats($alert->days_unit);

                switch($alert->notification_flag) {
                    case 'continuative_lateness':
                        if($attendanceStat['continuative_lateness'] >= $alert->count) {
                            $student->reason = "연속 지각 {$alert->count}회";
                            $student->sign_in_time = $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'continuative_leave':
                        if($attendanceStat['continuative_early_leave'] >= $alert->count) {
                            $student->reason = "연속 결석 {$alert->count}회";
                            $student->sign_in_time = $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'continuative_absence':
                        if($attendanceStat['continuative_early_leave'] >= $alert->count) {
                            $student->reason = "연속 조퇴 {$alert->count}회";
                            $student->sign_in_time = $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'total_lateness':
                        if($attendanceStat['total_lateness'] >= $alert->count) {
                            $student->reason = "누적 지각 {$alert->count}회";
                            $student->sign_in_time = $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'total_early_leave':
                        if($attendanceStat['total_early_leave'] >= $alert->count) {
                            $student->reason = "누적 조퇴 {$alert->count}회";
                            $student->sign_in_time = $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'total_absence':
                        if($attendanceStat['total_absence'] >= $alert->count) {
                            $student->reason = "누적 결석 {$alert->count}회";
                            $student->sign_in_time = $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
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
            'need_care' => $attendanceRecords['need_care'],
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    // 사랑이 필요한 학생 필터링 조건 설정
    public function setNeedCareAlert(Request $request) {
        // 01. 요청 유효성 검증
        $validAdaType = implode(',', self::ADA_TYPE);
        $validator = Validator::make($request->all(), [
            'days_unit'             => 'required|numeric|min:1|max:999',
            'ada_type'              => "required|in:{$validAdaType}",
            'continuative_flag'     => 'required|boolean',
            'count'                 => 'required|numeric|min:1|max:999',
            'alert_std_flag'        => 'required|boolean'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 설정
        $professor      = Professor::findOrFail(session()->get('user')->id);

        // 알림 조건
        $notificationFlag   = null;
        $continuativeFlag   = $request->post('continuative_flag');
        switch($request->post('ada_type')) {
            case 'lateness':
                if($continuativeFlag)
                    $notificationFlag = 'continuative_lateness';
                else
                    $notificationFlag = 'total_lateness';
                break;
            case 'absence':
                if($continuativeFlag)
                    $notificationFlag = 'continuative_absence';
                else
                    $notificationFlag = 'total_absence';
                break;
            case 'early_leave':
                if($continuativeFlag)
                    $notificationFlag = 'continuative_early_leave';
                else
                    $notificationFlag = 'total_early_leave';
                break;
        }

        // 03. 데이터 저장
        $model = new NeedCareAlert();

        $model->manager             = $professor->id;
        $model->days_unit           = $request->post('days_unit');
        $model->notification_flag   = $notificationFlag;
        $model->count               = $request->post('count');
        $model->alert_std_flag      = $request->post('alert_std_flag');

        if($model->save()) {
            return response()->json(new ResponseObject(
                true, "알림을 등록하였습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "알림 등록에 실패하였습니다."
            ), 200);
        }
    }

    // 사랑이 필요한 학생 필터링 조건 목록 조회
    public function getNeedCareAlertList() {
        // 01. 데이터 획득
        $professor = Professor::findOrFail(session()->get('user')->id);
        $alertList = $professor->needCareAlerts;

        // 데이터 설정
        foreach($alertList as $alert) {
            switch($alert->notification_flag) {
                case 'continuative_lateness':
                    $alert->continuative_flag   = true;
                    $alert->ada_type            = 'lateness';
                    break;
                case 'continuative_absence':
                    $alert->continuative_flag   = true;
                    $alert->ada_type            = 'absence';
                    break;
                case 'continuative_early_leave':
                    $alert->continuative_flag   = true;
                    $alert->ada_type            = 'early_leave';
                    break;
                case 'total_lateness':
                    $alert->continuative_flag   = false;
                    $alert->ada_type            = 'lateness';
                    break;
                case 'total_absence':
                    $alert->continuative_flag   = false;
                    $alert->ada_type            = 'absence';
                    break;
                case 'total_early_leave':
                    $alert->continuative_flag   = false;
                    $alert->ada_type            = 'early_leave';
                    break;
            }
            unset($alert->notification_flag);
        }

        // ##### 조회된 알림 조건이 없을 경우 #####
        if(sizeof($alertList) <= 0) {
            return response()->json(new ResponseObject(
                true, null
            ), 200);
        }

        return response()->json(new ResponseObject(
            true, $alertList
        ), 200);
    }

    // 사랑이 필요한 학생 필터링 조건 삭제
    public function deleteNeedCareAlert(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'alert_id'  => 'required|exists:need_care_alerts,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $alert      = $professor->isMyNeedCareAlert($request->post('alert_id'));

        // 03. 알림 삭제
        if($alert->delete()) {
            return response()->json(new ResponseObject(
                true, "알림을 삭제하였습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "알림 삭제가 실패하였습니다."
            ), 200);
        }
    }


    // 학생 관리

    // 내 지도학생 목록 조회
    public function getMyStudentsList(Request $request) {
        // 01. 데이터 획득
        $professor = Professor::find(session()->get('user')->id);

    }

}
