<?php

namespace App\Http\Controllers;

use App\NeedCareAlert;
use App\Score;
use Validator;
use App\Exceptions\NotValidatedException;
use Illuminate\Http\Request;
use App\Professor;
use App\Student;
use App\User;
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

    // 출결알림 조건 설정
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

    // 출결알림 목록 조회
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

    // 출결알림 조건 삭제
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



    // 학생 분석

    // 학생 분류기준 조회
    public function getCriteriaOfEvaluation(Request $request) {

    }

    // 학생 분류기준 수정
    public function updateCriteriaOfEvaluation(Request $request) {

    }

    // 유형별 학생 리스트 출력
    public function getStudentListOfType(Request $request) {
        // 01. 요청 유효성 검사
        $validType  = implode(',', ['total', 'filter', 'attention']);
        $validOrder = implode(',' , ['id', 'name']);
        $validator  = Validator::make($request->all(), [
            'type'      => "required|in:{$validType}",
            'order'     => "required|in:{$validOrder}"
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor      = Professor::findOrFail(session()->get('user')->id);
        $studyClass     = $professor->studyClass;
        $students       = $studyClass->students->all();
        $type           = $request->post('type');
        $order          = $request->post('order');

        // 03. 분류 기준에 따른 학생 분류
        foreach($students as $student) {
            $trouble = [];
            // 03-01. 출석 분석
            $attendances = $student->attendances()->start(today()->subDays($studyClass->ada_search_period))
                ->end(today()->format('Y-m-d'));

            // 지각
            if(($latenessCount = with(clone $attendances)->lateness()->count()) >= $studyClass->lateness_count) {
                $trouble[__('ada.ada')][] = __('tutor.lateness_count', ['count' => $latenessCount]);
            }

            // 조퇴
            if(($earlyLeaveCount = with(clone $attendances)->earlyLeave()->count()) >= $studyClass->early_leave_count) {
                $trouble[__('ada.ada')][] = __('tutor.early_leave_count', ['count' => $earlyLeaveCount]);
            }

            // 결석
            if(($absenceCount = with(clone $attendances)->absence()->count()) >= $studyClass->absence_count) {
                $trouble[__('ada.ada')][] = __('tutor.absence_count', ['count' => $absenceCount]);
            }


            // 03-02. 하위권 학생 & 최근 문제 학생 분류
            // 전공 & 일본어의 수강목록 획득
            $japaneseSubjects       = $student->selectSubjectList()->japanese()->pluck('id')->all();
            $majorSubjects          = $student->selectSubjectList()->major()->pluck('id')->all();

            // 문제점을 분석하는 알고리즘 정의
            $algorithm = function($type, $subjects) use ($studyClass, $student) {
                // 1. 데이터 정의
                $scores = Score::whereIn('subject_id', $subjects)
                    ->orderBy('execute_date', 'desc')->limit($studyClass->study_usual)->get()->all();
                $result = [];

                $standingOrders = [];
                $gainedScores = [];
                $averageScores = [];
                $recentStandingOrders = [];
                $recentGainedScores = [];
                foreach ($scores as $key => $score) {
                    $tempScore = $score->selectGainedScore($student->id);

                    // 평균 석차백분율 & 평균 성적을 획득
                    array_push($standingOrders, $tempScore->standing_order);
                    array_push($gainedScores,
                        number_format(($tempScore->score / $score->perfect_score) * 100, 0));
                    array_push($averageScores, $score->average_score);

                    // 최근 석차백분율 & 평균성적을 획득
                    if ($key < $studyClass->study_recent) {
                        array_push($recentStandingOrders, $tempScore->standing_order);
                        array_push($recentGainedScores,
                            number_format(($tempScore->score / $score->perfect_score) * 100, 0));
                    }
                }

                // 일본어 강의에 대한 분류
                // 석차백분율 대조
                $standingOrder = number_format(array_sum($standingOrders) / sizeof($standingOrders), 2);
                $recentStandingOrder = number_format(array_sum($recentStandingOrders) / sizeof($recentStandingOrders), 2);
                // 하위권 판단
                if ((1 - $studyClass->low_reflection) <= $standingOrder) {
                    $result[__('tutor.low_level')][] = __("tutor.{$type}_low_ref", ['ref' => $standingOrder * 100]);
                }
                // 최근 문제 판단
                if (($standingOrderGap = $recentStandingOrder - $standingOrder) >= $studyClass->recent_reflection) {
                    $result[__('tutor.recent_trouble')][] = __("tutor.{$type}_recent_ref", ['ref' => $standingOrderGap * 100]);
                }

                /*
                // 반평균 대비 성적 대조
                $averageScore = number_format(array_sum($averageScores) / sizeof($averageScores), 0);
                $gainedScore = number_format(array_sum($gainedScores) / sizeof($gainedScores), 0);
                $recentGainedScore = number_format(array_sum($recentGainedScores) / sizeof($recentGainedScores), 0);
                // 하위권 판단
                if ($averageScore >= $gainedScore + $studyClass->low_score) {
                    $scoreGap = $averageScore - $gainedScore;
                    $result[__('tutor.low_level')][] = __("tutor.{$type}_low_score", ['point' => $scoreGap]);
                }
                // 최근 문제 판단
                if ($gainedScore >= $recentGainedScore + $studyClass->recent_score) {
                    $scoreGap = $gainedScore - $recentGainedScore;
                    $result[__('tutor.recent_trouble')][] = __("tutor.{$type}_recent_score", ['point' => $scoreGap]);
                }*/

                return $result;
            };

            // 각 과목유형에 대한 문제점 분석
            $japaneseTrouble    = $algorithm('japanese', $japaneseSubjects);
            $majorTrouble       = $algorithm('major', $majorSubjects);
            $studyTrouble       = array_merge_recursive($japaneseTrouble, $majorTrouble);


            // 03-03. 출석분석 & 학업분석 결과를 병합
            $trouble = array_merge_recursive($trouble, $studyTrouble);

            // 03-04. 학생에 대한 필요 정보를 결합
            $stdInfo            = with(clone $student)->user->selectUserInfo();
            $student->name      = $stdInfo->name;
            $student->photo_url = $stdInfo->photo_url;
            $student->trouble   = $trouble;
        }

        // 조회 유형에 따른 학생 데이터 필터링
        switch($type) {
            case 'filter':
                $students = array_filter($students, function($value) {
                    return sizeof($value->trouble) > 0;
                });
                break;
            case 'attention':
                $students = array_filter($students, function($value) {
                    return $value->attention_level > 0;
                });
                break;
        }

        // 정렬
        usort($students, function($a, $b) use ($order) {
            switch($order) {
                case 'id':
                    if($a->id == $b->id) return 0;

                    return $a->id > $b->id ? 1 : -1;
                case 'name':
                    if($a->name == $b->name) return 0;

                    return strcmp($a->name, $b->name);
            }
        });

        // 응답
        return response()->json(new ResponseObject(
            true, $students
        ), 200);
    }

    // 분석에 사용할 속성 목록 획득
    public function getOptionList(Request $request) {

    }

    // 출석 그래프 데이터 획득
    public function getDataOfAdaGraph(Request $request) {

    }

    // 학업 그래프 데이터 획득
    public function getDataOfStudyGraph(Request $request) {

    }



    // 학생 관리

    // 내 지도학생 목록 조회
    public function getMyStudentsList(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'period'        => 'regex:/^[1-2]\d{3}-[1-2]?[a-zA-Z_]+$/'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::find(session()->get('user')->id);
        $students   = $professor->studyClass->students()->get()->all();
        $argPeriod  = $request->exists('period') ? $request->get('period') : null;
        $period     = $this->getTermValue($argPeriod);

        // 학업성취도 획득
        foreach($students as $student) {
            //$joinList = Student::findOrFail($student->id)->joinLists()->period($period['this']);

            unset($student->study_class);
            $student->name  = User::findOrFail($student->id)->name;
            $student->photo = User::findOrFail($student->id)->selectUserInfo()->photo_url;
            //$student->average_achievement = number_format(with(clone $joinList)->avg('achievement') * 100, 0);
            //$student->minimum_achievement = number_format(with(clone $joinList)->min('achievement') * 100, 0);
        }

        // 페이지네이션 설정
        $pagination = [
            'prev'  => $period['prev'],
            'this'  => $period['this_format'],
            'next'  => $period['next']
        ];

        // 03. View 단에 전달할 데이터 설정
        $data = [
            'students'      => $students,
            'pagination'    => $pagination
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    // 학생별 상세 관리
    // 해당 학생의 출결 통계 목록 획득
    public function getDetailsOfAttendanceStats(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id',
            'period'        => 'required_with:date|in:weekly,monthly',
            'date'          => 'regex:/^[1-2]\d{3}-\d{2}$/'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $student    = $professor->isMyStudent($request->get('std_id'));
        $argPeriod  = $request->exists('period') ? $request->get('period') : 'monthly';
        $argDate    = $request->exists('date') ? $request->get('date') : null;

        // 03. 출석 데이터 조회
        $attendances    = null;
        $date           = null;
        $pagination     = null;
        switch($argPeriod) {
            case 'weekly':
                // 주 단위 조회결과를 획득
                $date = $this->getWeeklyValue($argDate);
                $attendances = $student->attendances()
                    ->start($date['this']->copy()->startOfWeek()->format('Y-m-d'))
                    ->end($date['this']->copy()->endOfWeek()->format('Y-m-d'));

                // 페이지네이션용 데이터 획득
                $prevWeek = sprintf('%04d-%02d', $date['prev']->year, $date['prev']->weekOfYear);
                $nextWeek = is_null($date['next']) ? null :
                    sprintf('%04d-%02d', $date['next']->year, $date['next']->weekOfYear);
                $pagination = [
                    'prev'      => $prevWeek,
                    'this'      => $date['this_format'],
                    'next'      => $nextWeek,
                    'period'    => $argPeriod
                ];
                break;

            case 'monthly':
                // 월 단위 조회 결과를 획득
                $date = $this->getMonthlyValue($argDate);
                $attendances = $student->attendances()
                    ->start($date['this']->copy()->startOfMonth()->format('Y-m-d'))
                    ->end($date['this']->copy()->endOfMonth()->format('Y-m-d'));

                // 페이지네이션용 데이터 획득
                $prevMonth = sprintf("%02d", $date['prev']->month);
                $nextMonth = is_null($date['next']) ? null : sprintf("%02d", $date['next']->month);

                $pagination = [
                    'prev'  => "{$date['prev']->year}-{$prevMonth}",
                    'this'  => $date['this_format'],
                    'next'  => is_null($nextMonth) ? null : "{$date['next']->year}-{$nextMonth}",
                    'period'    => $argPeriod
                ];
                break;
        }

        // ##### 조회 결과가 없을 경우 #####
        if(with(clone $attendances)->count() <= 0) {
            return response()->json(new ResponseObject(
                false, "조회된 출석기록이 없습니다."
            ), 200);
        }

        // 오늘자 출석 기록 조회
        $selectDate = Carbon::create()->hour > 6 ? today()->format('Y-m-d') : today()->subDay()->format('Y-m-d');
        $todayAda = $student->attendances()->start($selectDate)->end($selectDate)->get()->all();
        if(sizeof($todayAda) > 0) {
            $todayAda = $todayAda[0];
        } else {
            $todayAda = null;
        }

        // 연속 기록 조회
        $daysUnit = null;
        switch($argPeriod) {
            case 'weekly':
                $daysUnit = 7;
                break;
            case 'monthly':
                $daysUnit = $date['this']->copy()->endOfMonth()->diffInDays($date['this']->copy()->startOfmonth());
                break;
        }
        $continuativeData = $student->selectAttendancesStats($daysUnit);


        // 04. view 단에 전달할 데이터 설정
        $data = [
            // 총 출석횟수
            'total_sign_in'                     => with(clone $attendances)->signIn()->count(),

            // 총 지각횟수
            'total_lateness'                    => with(clone $attendances)->lateness()->count(),

            // 총 결석횟수
            'total_absence'                     => with(clone $attendances)->absence()->count(),

            // 총 조퇴횟수
            'total_early_leave'                 => with(clone $attendances)->earlyLeave()->count(),

            // 오늘 등교일시
            'today_sign_in'                     => is_null($todayAda) ? null : $todayAda->sign_in_time,

            // 오늘 하교일시
            'today_sign_out'                    => is_null($todayAda) ? null : $todayAda->sign_out_time,

            // 연속 지각횟수
            'continuative_lateness'             => $continuativeData['continuative_lateness'],

            // 연속 결석횟수
            'continuative_absence'              => $continuativeData['continuative_absence'],

            // 연속 조퇴횟수
            'continuative_early_leave'          => $continuativeData['continuative_early_leave'],

            // 최근 지각일자
            'recent_lateness'                   => with(clone $attendances)->lateness()->max('reg_date'),

            // 최근 결석일자
            'recent_absence'                    => with(clone $attendances)->absence()->max('reg_date'),

            // 최근 조퇴일자
            'recent_early_leave'                => with(clone $attendances)->earlyLeave()->max('reg_date'),

            // 페이지네이션용 데이터
            'pagination'                        => $pagination,
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    // 모바일 : 학생 출결정보 그래프 출력
    public function getGraphOfAttendance(Request $request) {
        // 01. 데이터 획득
        $data = $this->getDetailsOfAttendanceStats($request)->original->message;

        // 02. 웹 페이지 반환
        return view('student_attendance_graph', [
            'sign_in'       => $data['total_sign_in'],
            'lateness'      => $data['total_lateness'],
            'absence'       => $data['total_absence'],
            'early_leave'   => $data['total_early_leave'],
        ]);
    }

    // 해당 학생의 출석 데이터 목록 획득
    public function getDetailsOfAttendanceRecords(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $student    = $professor->isMyStudent($request->get('std_id'));
        $attendance = $student->attendances()->orderBy('reg_date', 'desc')->get([
            'reg_date', 'sign_in_time', 'sign_out_time', 'lateness_flag', 'early_leave_flag', 'absence_flag', 'detail'
        ])->all();

        // ##### 조회된 출석 기록이 없을 때 ######
        if(sizeof($attendance) <= 0) {
            return response()->json(new ResponseObject(
                false, "조회된 출석 기록이 없습니다."
            ), 200);
        }

        return response()->json(new ResponseObject(
            true, $attendance
        ), 200);
    }

    // 해당 학생의 출결 분석 결과 획득
    public function getDetailsOfAnalyseAttendance(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $student    = $professor->isMyStudent($request->get('std_id'));

        // 03. view 단에 반환할 데이터 설정
        $data = [
            // 요일별 (지각|결석|조퇴) 데이터 획득
            'frequent_data'     => $student->selectFrequentAttendances(),

            // 평균 지각 시각
            'lateness_average'  => $student->selectAverageLatenessTime(),

            // 월 평균 (지각|결석|조퇴) 횟수 획득
            'average_data'      => $student->selectMonthlyAverageAttendances(),

            // 주요 사유
            'reason'            => $student->selectAttendanceReason(),
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }
    

    // 해당 학생의 수강목록 획득
    public function getDetailsOfSubjects(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id',
            "period"        => 'regex:/^[1-2]\d{3}-[1-2]?[a-zA-Z_]+$/'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $student    = $professor->isMyStudent($request->get('std_id'));
        $argPeriod  = $request->exists('period') ? $request->get('period') : null;
        $periodData = $this->getTermValue($argPeriod);

        // 해당 학기의 과목 목록 획득
        $subjects   = $student->joinLists()->period($periodData['this'])
            ->join('subjects', 'join_lists.subject_id', 'subjects.id')->get(['subjects.id', 'subjects.name'])->all();

        $pagination = [
            'prev'  => $periodData['prev'],
            'this'  => $periodData['this_format'],
            'next'  => $periodData['next']
        ];

        // 03. view 단에 전달할 데이터 설정
        $data = [
            'subjects'      => $subjects,
            'pagination'    => $pagination
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    // 학생이 해당 강의에서 취득한 성적통계 획득
    public function getDetailsOfScoreStat(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id',
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $student    = $professor->isMyStudent($request->get('std_id'));
        $subject    = $student->isMySubject($request->get('subject_id'));

        // 03. view 단에 데이터 반환
        $data = [
            'stats'         => $student->selectStatList($subject->id),
            //'achievement'   => number_format($student->joinLists()->subject($subject->id)->get()[0]->achievement * 100, 0)
        ];

        return response()->json(new ResponseObject(
            200, $data
        ), 200);
    }

    // 학생이 해당 강의에서 취득한 성적 목록 조회
    public function getDetailsOfScoreList(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'        => 'required|exists:students,id',
            'subject_id'    => 'required_without:period|exists:subjects,id',
            'period'        => 'required_without:subject_id|regex:/^[1-2]\d{3}-[1-2]?[a-zA-Z_]+$/'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $student    = $professor->isMyStudent($request->get('std_id'));
        $subject    = $request->exists('subject_id') ?
            $student->isMySubject($request->get('subject_id')) : null;

        $scores     = [];
        if(is_null($subject)) {
            // 과목을 지정하지 않았다면 => 해당 학기의 성적 목록을 출력
            $joinList = $student->joinLists()->period($request->get('period'))->get()->all();

            // 각 강의별 성적 목록 획득
            foreach($joinList as $join) {
                $scores = array_merge_recursive($scores, $student->selectScoresList($join->subject_id)->get()->all());
            }

            // 실시 일자에 따른 역순정렬
            uasort($scores, function($a, $b) {
                if($a->execute_date == $b->execute_date) return 0;

                return $a->execute_date < $b->execute_date ? 1 : -1;
            });
            $scores = array_merge($scores);

        } else {
            // 과목을 지정했다면 => 해당 과목의 성적 목록을 출력
            $scores = $student->selectScoresList($subject->id)->get()->all();
        }


        return response()->json(new ResponseObject(
            true, $scores
        ), 200);
    }


}
