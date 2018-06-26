<?php

namespace App\Http\Controllers;

use App\NeedCareAlert;
use App\Schedule;
use App\Score;
use App\Subject;
use App\Term;
use ArrayObject;
use GuzzleHttp\Client;
use Illuminate\Validation\Rule;
use Validator;
use App\Exceptions\NotValidatedException;
use Illuminate\Http\Request;
use App\Professor;
use App\Student;
use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


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
    public function getAttendanceRecordsOfToday(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'date'  => 'date_format:Y-m-d'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }


        // 02. 데이터 획득
        $professor          = Professor::findOrFail(session()->get('user')->id);
        $myStudents         = $professor->students()
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
        // 조회일자 획득
        $searchTime = null;
        if($request->has('date')) {
            // 조회일자 데이터를 수신했다면 해당 일자를 조회
            $searchTime = Carbon::parse($request->get('date'));
        } else {
            // 조회일자 지정 (등교 시작시간보다 이른 시간대면 전날 출석기록을 조회)
            $searchTime = now();
            $limitTime  = now()->setTimeFromTimeString($professor->studyClass->sign_in_time)->subMinutes(30);
            if ($searchTime->lt($limitTime)) {
                $searchTime->subDay();
            }
        }

        foreach($myStudents as $student) {
            // 학생별 정보 획득
            $stdInfo = $student->user->selectUserInfo();
            $url = $stdInfo->photo_url;
            $student->photo_url = $url;

            // 출석 데이터 획득
            $attendance = $student->attendances()->whereDate('reg_date', $searchTime->format('Y-m-d'));

            // ###### 조회된 출석 기록이 없다면 => 아직 학교에 안왔으므로 결석 ######
            if(!$attendance->exists()) {
//                $attendanceRecords['absence'][] = $student;
                $attendance = null;
            } else {
                $attendance = $attendance->first();
            }

            // 출결관리가 필요한 학생 필터링
            foreach($needCareAlerts as $alert) {
                $attendanceStat = $student->selectAttendancesStats($alert->days_unit);

                switch($alert->notification_flag) {
                    case 'continuative_lateness':
                        if($attendanceStat['continuative_lateness'] >= $alert->count) {
                            $student->reason = "연속 지각 {$alert->count}회";
                            $student->sign_in_time = is_null($attendance) ? null : $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'continuative_leave':
                        if($attendanceStat['continuative_early_leave'] >= $alert->count) {
                            $student->reason = "연속 결석 {$alert->count}회";
                            $student->sign_in_time = is_null($attendance) ? null : $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'continuative_absence':
                        if($attendanceStat['continuative_early_leave'] >= $alert->count) {
                            $student->reason = "연속 조퇴 {$alert->count}회";
                            $student->sign_in_time = is_null($attendance) ? null : $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'total_lateness':
                        if($attendanceStat['total_lateness'] >= $alert->count) {
                            $student->reason = "누적 지각 {$alert->count}회";
                            $student->sign_in_time = is_null($attendance) ? null : $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'total_early_leave':
                        if($attendanceStat['total_early_leave'] >= $alert->count) {
                            $student->reason = "누적 조퇴 {$alert->count}회";
                            $student->sign_in_time = is_null($attendance) ? null : $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                    case 'total_absence':
                        if($attendanceStat['total_absence'] >= $alert->count) {
                            $student->reason = "누적 결석 {$alert->count}회";
                            $student->sign_in_time = is_null($attendance) ? null : $attendance->sign_in_time;
                            $attendanceRecords['need_care'][] = $student;
                            continue 3;
                        }
                        break;
                }
            }

            // 결석, 지각, 등교, 하교 필터링
            if(is_null($attendance)) {
                $attendanceRecords['absence'][] = $student;
            } else if($attendance->lateness_flag != 'good') {
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

        // 페이지네이션 값 설정
        $dateInfo = $this->getDailyValue($searchTime->format('Y-m-d'));
        $pagination = [
            'prev'  => $dateInfo['prev']->format('Y-m-d'),
            'this'  => $dateInfo['this_format'],
            'next'  => is_null($dateInfo['next']) ? null : $dateInfo['next']->format('Y-m-d')
        ];


        // 데이터 추출 결과값을 반환
        $data = [
            'sign_in'       => $attendanceRecords['sign_in'],
            'lateness'      => $attendanceRecords['lateness'],
            'absence'       => $attendanceRecords['absence'],
            'sign_out'      => $attendanceRecords['sign_out'],
            'need_care'     => $attendanceRecords['need_care'],
            'pagination'    => $pagination,
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
    public function getCriteriaOfEvaluation() {
        // 01. 데이터 획득
        $professor  = Professor::findOrFail(session()->get("user")->id);
        $criteria   = $professor->studyClass()->get([
            'ada_search_period', 'lateness_count', 'early_leave_count', 'absence_count',
            'study_usual', 'study_recent', 'low_reflection', 'low_score', 'recent_reflection', 'recent_score'
        ])->all()[0];

        $criteria->low_reflection *= 100;
        $criteria->recent_reflection *= 100;

        return response()->json(new ResponseObject(
            true, $criteria
        ), 200);
    }

    // 학생 분류기준 수정
    public function updateCriteriaOfEvaluation(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'ada_search_period'     => 'required|numeric|min:1|max:999',
            'lateness_count'        => 'required|numeric|min:1|max:999',
            'early_leave_count'     => 'required|numeric|min:1|max:999',
            'absence_count'         => 'required|numeric|min:1|max:999',
            'study_usual'           => 'required|numeric|min:1|max:999',
            'study_recent'          => 'required|numeric|min:1|max:999',
            'low_reflection'        => 'required|numeric|min:0|max:100',
            'low_score'             => 'required|numeric|min:0|max:100',
            'recent_reflection'     => 'required|numeric|min:0|max:100',
            'recent_score'          => 'required|numeric|min:0|max:100'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $studyClass = $professor->studyClass;
        $data = new ArrayObject($request->all());
        $data['low_reflection'] /= 100;
        $data['recent_reflection'] /= 100;

        if($studyClass->updateCriteria($data->getArrayCopy())) {
            return response()->json(new Responseobject(
                true, "갱신 성공하였습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, '갱신에 실패하였습니다.'
            ), 200);
        }
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
        $students       = $professor->students->all();
        $type           = $request->get('type');
        $order          = $request->get('order');

        // 03. 분류 기준에 따른 학생 분류
        foreach($students as $student) {
            $trouble = [];
            // 03-01. 출석 분석
            $attendances = $student->attendances()->start(today()->subDays($studyClass->ada_search_period))
                ->end(today()->format('Y-m-d'));

            // 지각
            if(($latenessCount = with(clone $attendances)->lateness()->count()) >= $studyClass->lateness_count) {
                $trouble['ada'][] = __('tutor.lateness_count', ['count' => $latenessCount]);
            }

            // 조퇴
            if(($earlyLeaveCount = with(clone $attendances)->earlyLeave()->count()) >= $studyClass->early_leave_count) {
                $trouble['ada'][] = __('tutor.early_leave_count', ['count' => $earlyLeaveCount]);
            }

            // 결석
            if(($absenceCount = with(clone $attendances)->absence()->count()) >= $studyClass->absence_count) {
                $trouble['ada'][] = __('tutor.absence_count', ['count' => $absenceCount]);
            }


            // 03-02. 하위권 학생 & 최근 문제 학생 분류
            // 전공 & 일본어의 수강목록 획득
            $japaneseSubjects       = $student->subjects()->japanese()->pluck('subjects.id')->all();
            $majorSubjects          = $student->subjects()->major()->pluck('subjects.id')->all();

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
                    $result['low_level'][] = __("tutor.{$type}_low_ref", ['ref' => $standingOrder * 100]);
                }
                // 최근 문제 판단
                if (($standingOrderGap = $recentStandingOrder - $standingOrder) >= $studyClass->recent_reflection) {
                    $result['recent_trouble'][] = __("tutor.{$type}_recent_ref", ['ref' => $standingOrderGap * 100]);
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
            $stdInfo                    = with(clone $student)->user->selectUserInfo();
            $student->name              = $stdInfo->name;
            $student->photo_url         = $stdInfo->photo_url;
            $student->attention_reason  = $stdInfo->attention_reason;
            $student->trouble           = $trouble;
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

    public function getDateValue($periodType, $start, $end) {
        // 01. 매개인자 유효성 검사
        $validPeriodType = implode(',', ['daily', 'weekly', 'monthly', 'recently']);
        $validator = Validator::make(['period_type' => $periodType, 'start_date' => $start, 'end_date' => $end], [
            // 기간 선택
            'period_type'   => "required|in:{$validPeriodType}",
            'start_date'    => 'required_unless:period_type,recently',
            'end_date'      => 'required_unless:period_type,recently',
        ]);

        // 기간 유형별 날짜 형식 지정
        // 일별
        $validator->sometimes(['start_date', 'end_date'], [
            'date'
        ], function($input) {
            return $input->period_type == 'daily';
        });

        // 주별
        $validator->sometimes(['start_date', 'end_date'], [
            'regex:/(19|20)\d{2}-(0[1-9]|[1234][0-9]|5[012])/'
        ], function($input) {
            return $input->period_type == 'weekly';
        });

        // 월별
        $validator->sometimes(['start_date', 'end_date'], [
            'regex:/(19|20)\d{2}-(0[1-9]|1[012])/'
        ], function($input) {
            return $input->period_type == 'monthly';
        });


        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 날짜 데이터 획득
        switch($periodType) {
            case 'daily':
                // 일 단위 조회
                $startDate  = Carbon::parse($start);
                $endDate    = Carbon::parse($end);
                $period     = 'day';
                break;
            case 'weekly':
                $startDate  = $this->getWeeklyValue($start)['this'];
                $endDate    = $this->getWeeklyValue($end)['this'];
                $period     = 'week';
                break;
            case 'monthly':
                $startDate  = $this->getMonthlyValue($start)['this'];
                $endDate    = $this->getMonthlyValue($end)['this'];
                $period     = 'month';
                break;
            case 'recently':
                // 최근 기간 조회
                $startDate  = today()->subWeeks(10);
                $endDate    = today();
                $period     = 'week';
                break;
        }
        if($startDate->gt($endDate)) {
            throw new NotValidatedException('시작일은 종료일보다 과거여야 합니다.');
        }

        // 값 반환
        return [
            'start'     => $startDate,
            'end'       => $endDate,
            'period'    => $period
        ];
    }

    // 해당 학생의 분석조건 옵션 출력 => 해당 기간동안 수강한 강의목록을 출력
    public function getOptionForStudent(Request $request) {
        // 01. 요청 유효성 검사
        $validPeriodType = implode(',', ['daily', 'weekly', 'monthly', 'recently']);
        $validator = Validator::make($request->all(), [
            'std_id'        => 'exists:students,id',
            'period_type'   => "required|in:{$validPeriodType}",
            'start_date'    => 'required_unless:period_type,recently',
            'end_date'      => 'required_unless:period_type,recently',
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get("user")->id);
        $student    = $request->exists('std_id') ? $professor->isMyStudent($request->get('std_id')) : null;

        $periodType = $request->get('period_type');
        $dateData   = $this->getDateValue($periodType, $request->get('start_date'), $request->get('end_date'));
        $startDate  = $dateData['start'];
        $endDate    = $dateData['end'];

        $terms      = Term::termsIncludePeriod($startDate->format('Y-m-d'), $endDate->format('Y-m-d'))->get()->all();
        $subjects   = [];

        // 각 학기별로 수강한 강의를 조회
        foreach($terms as $term) {
            if(!is_null($student)) {
                // 학번을 수신했을 때 -> 해당 학생의 수강 목록을 조회
                $joinSubject = $student->subjects()->where([['year', $term->year], ['term', $term->term]])
                    ->get()->all();
            } else {
                // 학번을 수신하지 않았을 때 => 내 반의 수강목록을 조회
                $joinSubject = $professor->studyClass->subjects()->where([['year', $term->year], ['term', $term->term]])
                    ->get()->all();

                // 해당 강의에서 제출된 성적 목록을 조회
                foreach($joinSubject as $key => $value) {
                    $scores = $value->scores()->orderBy('execute_date', 'desc')
                        ->get(['id', 'execute_date', 'detail', 'type'])->all();

                    // 다국어 언어팩 적용
                    foreach($scores as $scoreKey => $scoreValue) {
                        $scores[$scoreKey]->type = __("study.{$scoreValue->type}");
                    }
                    $joinSubject[$key]->scores = $scores;
                }
            }

            $subjects = array_merge($subjects, $joinSubject);
        }

        return response()->json(new ResponseObject(
            true, $subjects
        ), 200);
    }

    // 조건 조합에 의한 분석 결과 반환

    /**localhost:8000/admin/schedule/select
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws NotValidatedException
     */
    public function getDataOfGraph(Request $request)
    {
        // 01. 요청 유효성 확인
        $validMajorClass = implode(',', ['ada', 'study']);

        $validMinorClass = [
            'ada' => implode(',', [
                'lateness', 'early_leave', 'absence', 'sign_in', 'sign_out', 'holiday'
            ]),
            'subject_type' => implode(',', [
                'japanese', 'major'
            ]),
            'study_type' => implode(',' ,[
                'midterm', 'final', 'quiz', 'homework'
            ])
        ];

        $validPeriodType = implode(',', [
            'daily', 'weekly', 'monthly', 'recently'
        ]);

        $validGraphType = implode(',', [
            'single_line', 'double_line', 'compare', 'pie', 'donut', 'box_and_whisker', 'histogram'
        ]);

        $validator = Validator::make($request->all(), [
            // 유형 조합
            'major_class'   => "required|in:{$validMajorClass}",

            // 기간 선택
            'period_type'   => "required|in:{$validPeriodType}",
            'start_date'    => 'required_unless:period_type,recently',
            'end_date'      => 'required_unless:period_type,recently',

            // 그래프 유형 선택
            'graph_type'    => "required|in:{$validGraphType}",

            // 대상 학생
            'std_id'        => 'exists:students,id'
        ]);

        // 대분류가 출석 유형인 경우 => 소분류 지점
        $validator->sometimes('minor_class', [
            "in:{$validMinorClass['ada']}"
        ], function ($input) {
            return $input->major_class == 'ada';
        });

        // 대분류가 학업 유형인 경우 => 소분류 지점
        $validator->sometimes('minor_class', [
            'regex:/\d+_(homework|quiz|midterm|final)|subject_\d+|score_\d+|japanese|major/'
        ], function($input) {
            return $input->major_class == 'study';
        });

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor = Professor::findOrFail(session()->get('user')->id);
        $student = $request->has('std_id') ? $professor->isMyStudent($request->get('std_id')) : null;

        $majorClass = $request->get('major_class');
        $periodType = $request->get('period_type');
        $minorClass = $request->has('minor_class') ? $request->get('minor_class') : null;
        $graphType = $request->get('graph_type');

        // 기간 데이터 구하기
        $dateData   = $this->getDateValue($periodType, $request->get('start_date'), $request->get('end_date'));
        $startDate  = $dateData['start'];
        $endDate    = $dateData['end'];
        $period     = $dateData['period'];

        $ucPeriod   = ucfirst($period);

        $result = [];

        // 03. 지정 옵션에 따른 데이터 도출
        if (is_null($student)) {
            // 지도반 분석
            switch($majorClass) {
                case 'ada':
                    // 출석 데이터 분석
                    // 소분류에 따른 분석 알고리즘 분류
                    switch($minorClass) {
                        case 'lateness':
                        case 'early_leave':
                        case 'absence':
                            // (지각|조퇴|결석) 분석
                            // 그래프 유형에 따른 분석 결과 획득
                            switch($graphType) {
                                case 'pie':
                                    // 파이 : (지각|조퇴|결석) 횟수별 비율 구하기
                                    $query = DB::select("
                                                select out_s.id, u.name, ifnull(count, 0) as count
                                                from students out_s
                                                left join (
                                                    select std_id, count(*) as count
                                                    from students in_s
                                                    join attendances ada
                                                    on in_s.id = ada.std_id
                                                    where ada.reg_date >= '{$startDate->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d')}'
                                                    and ada.reg_date <= '{$endDate->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d')}'
                                                    and ada.{$minorClass}_flag != 'good'
                                                    group by std_id
                                                ) a
                                                on out_s.id = a.std_id
                                                join study_classes sc
                                                on out_s.study_class = sc.id
                                                join professors p
                                                on p.id = sc.tutor
                                                join users u
                                                on u.id = out_s.id
                                                where p.id = '{$professor->id}'
                                    ");

                                    // 그래프 데이터 구하기
                                    $graph = [];
                                    foreach($query as $value) {
                                        // 단위 설정
                                        $unit = $minorClass == 'absence' ? 1 : 5;
                                        $temp = intval($value->count / $unit);

                                        // 횟수 단위별 인원수 도출
                                        // 결석인 경우 : 1번 단위로 끊어서 출력 / 그 이외: 5번 단위로 끊어서 출력
                                        $key = $minorClass == 'absence' ? $temp : ($temp * 5). '~' .(($temp * $unit) + ($unit - 1));
                                        if(isset($graph[$temp])) {
                                            $graph[$temp]['name'] = __('ada.count', ['count' => $key]);
                                            $graph[$temp]['count']++;
                                        } else {
                                            $graph[$temp]['count'] = 1;
                                        }
                                        $graph[$temp]['detail'][] = $value;

                                        // 그래프 정렬
                                        //arsort($graph);
                                    }

                                    $result['value'] = $graph;
                                    $result['length'] = sizeof($graph);
                                    break;

                                case 'single_line':
                                    // 단일 꺾은선 : 평균 (지각|조퇴|결석) 인원
                                    // 기간 단위별 데이터 획득
                                    $flag = "{$minorClass}_flag";
                                    for($dateCount = $startDate->copy(); $dateCount->lte($endDate); $dateCount->{"add{$ucPeriod}"}()) {
                                        // 기간 단위에 따른 휴일 제외 알고리즘 변경
                                        $count = null;
                                        switch($periodType) {
                                            case 'daily':
                                                // 일 단위 조회시 => 해당 일자가 휴일인 경우 통과
                                                if ($professor->studyClass->isHolidayAtThisDay($dateCount->format('Y-m-d'))) {
                                                    continue 2;
                                                }
                                                $count = 1;
                                                break;

                                            case 'weekly':
                                            case 'recently':
                                            case 'monthly':
                                                // 주/월 단위 조회시 => 총 평일수를 조회
                                                $holidays = $professor->studyClass->selectHolidaysList(
                                                    $dateCount->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'),
                                                    $dateCount->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d')
                                                );

                                                $count = ($dateCount->copy()->{"startOf{$ucPeriod}"}()
                                                            ->diffInDays($dateCount->copy()->{"endOf{$ucPeriod}"}()) + 1) - sizeof($holidays);

                                                break;
                                        }


                                        // 질의 조건문 설정
                                        $joinWhere = [
                                            // 조회 시작/종료 기간 설정
                                            ['attendances.reg_date', '>=', $dateCount->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d')],
                                            ['attendances.reg_date', '<=', $dateCount->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d')],

                                            // 출석 유형 설정
                                            [$flag, '!=', 'good']
                                        ];

                                        $query = $professor->students()->leftJoin('attendances', function($join) use($joinWhere) {
                                            $join->on('students.id', 'attendances.std_id')->where($joinWhere); })
                                            ->join('users', 'users.id', 'students.id')
                                            ->whereNotNull('attendances.reg_date')
                                            ->orderBy('attendances.reg_date');

                                        // 질의 결과 획득
                                        $data = ['x-point' => null, 'y-point' => 0, 'detail' => null];
                                        if($query->exists()) {
                                            // 상세정보 조회문 설정
                                            $selectList         = ['attendances.std_id', 'users.name', "attendances.{$flag}"];
                                            switch($minorClass) {
                                                case 'lateness':
                                                    $selectList[] = "attendances.sign_in_time";
                                                    break;
                                                case 'early_leave':
                                                    $selectList[] = "attendances.sign_out_time";
                                                    break;
                                            }

                                            $data['y-point']    = round($query->count() / $count, 1);
                                            $detail             = with(clone $query)->select($selectList)->get()->all();

                                            foreach($detail as $key => $val) {
                                                $detail[$key]->{$flag} = __("ada.{$val->{$flag}}");
                                            }

                                            $data['detail'] = $detail;
                                        }

                                        // x축 데이터 설정
                                        switch($periodType) {
                                            case 'daily':
                                                $data['x-point'] = $dateCount->format('Y-m-d');
                                                break;
                                            case 'weekly':
                                            case 'recently':
                                                $data['x-point'] = sprintf("%d-%02d-%d",
                                                    $dateCount->year, $dateCount->month, $dateCount->weekOfMonth);
                                                break;
                                            case 'monthly':
                                                $data['x-point'] = $dateCount->format('Y-m');
                                                break;
                                        }

                                        $result['value'][] = $data;
                                    }
                                    break;

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                            break;

                        case 'holiday':
                            // 휴일 등하교 관련 분석
                            // 그래프 유형에 따른 분석결과 획득
                            switch($graphType) {
                                case 'single_line':
                                    // 단일 꺾은선: 휴일에 등교한 인원
                                    // 기간 단위별 데이터 획득
                                    for($dateCount = $startDate->copy(); $dateCount->lte($endDate); $dateCount->{"add{$ucPeriod}"}()) {
                                        // 기간 조회단위가 일 단위인 경우 -> 조회일이 휴일이 아니면 통과
                                        if($periodType == 'daily') {
                                            if($professor->studyClass->isHolidayAtThisDay($dateCount->format('Y-m-d')) == false) {
                                                continue;
                                            }
                                        }

                                        // 휴일 목록 구하기
                                        $holidays = $professor->studyClass->selectHolidaysList(
                                            $dateCount->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'),
                                            $dateCount->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d')
                                        );

                                        // 질의 조건문 설정
                                        $dateListOfHoliday = array_keys($holidays);

                                        $query = $professor->students()
                                            ->leftJoin('attendances', function($join) use($dateListOfHoliday) {
                                                $join->on('students.id', 'attendances.std_id')
                                                    ->whereIn('reg_date', $dateListOfHoliday)
                                                    ->where([['lateness_flag', 'good'], ['early_leave_flag', 'good']]); })
                                            ->join('users', 'users.id', 'students.id')
                                            ->whereNotNull('attendances.reg_date')
                                            ->orderBy('attendances.reg_date')
                                            ->select(
                                                'attendances.std_id', 'users.name', 'attendances.reg_date',
                                                'attendances.sign_in_time', 'attendances.sign_out_time'
                                            );


                                        // 질의 데이터 획득
                                        $data = ['x-point' => null, 'y-point' => 0, 'detail' => null];
                                        if($query->exists()) {
                                            $data['y-point']    = round((with(clone $query)->count() / sizeof($holidays)), 1);

                                            // 상세 데이터 획득
                                            $detail = [];
                                            foreach($holidays as $date => $name) {
                                                $detail[] = [
                                                    'date' => $date,
                                                    'name' => $name,
                                                    'students' => with(clone $query)->where('attendances.reg_date', $date)->get()->all()
                                                ];
                                            }

                                            $data['detail']     = $detail;
                                        }

                                        // x축 데이터 설정
                                        switch($periodType) {
                                            case 'daily':
                                                $data['x-point'] = $dateCount->format('Y-m-d');
                                                break;
                                            case 'weekly':
                                            case 'recently':
                                                $data['x-point'] = sprintf("%d-%02d-%d",
                                                    $dateCount->year, $dateCount->month, $dateCount->weekOfMonth);
                                                break;
                                            case 'monthly':
                                                $data['x-point'] = $dateCount->format('Y-m');
                                                break;
                                        }

                                        $result['value'][] = $data;
                                    }
                                    break;

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                            break;

                        default:
                            throw new NotValidatedException("지원하지 않는 소분류입니다.");
                    }
                    break;



                case 'study':
                    // 학업 데이터 분석
                    // 소분류에 따른 분석 알고리즘 분류
                    switch($minorClass) {
                        case (preg_match('/subject_\d+/', $minorClass) ? true : false):
                            // 강의별 분석
                            $explode    = explode('_', $minorClass);
                            $subjectId  = $explode[1];

                            // 그래프 유형에 따른 분석결과 획득
                            switch($graphType) {
                                case 'box_and_whisker':
                                    // 상자수염그림
                                    $query = Subject::findOrFail($subjectId)->scores()
                                        ->start($startDate->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'))
                                        ->end($endDate->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d'))
                                        ->orderBy('execute_date', 'desc');
                                    // 해당 강의에서 제출된 성적에 대한 상자수염그림
                                    if($query->exists()) {
                                        // 해당 기간에 조회된 성적 데이터가 있을 경우
                                        foreach($query->get()->all() as $score) {
                                            $temp           = [];
                                            $gainedScores   = $score->gainedScores()->orderBy('score', 'desc');

                                            // 수치 설정
                                            $temp['y-point']['max'] = $gainedScores->max('score');
                                            $temp['y-point']['25%'] = with(clone $gainedScores)->get()[ceil($gainedScores->count() * 1/4)]->score;
                                            $temp['y-point']['avg'] = ceil($gainedScores->avg('score'));
                                            $temp['y-point']['75%'] = with(clone $gainedScores)->get()[ceil($gainedScores->count() * 3/4)]->score;
                                            $temp['y-point']['min'] = $gainedScores->min('score');

                                            // 이외의 값 설정
                                            $temp['score_id']       = $score->id;
                                            $temp['x-point']        = $score->execute_date;
                                            $temp['detail']['name'] = $score->detail;
                                            $temp['detail']['type'] = __("study.{$score->type}");

                                            $result['value'][] = $temp;
                                        }

                                    } else {
                                        // 해당 기간에 조회된 데이터가 없을 경우
                                        return response()->json(new ResponseObject(
                                            false, '조회된 데이터가 없습니다.'
                                        ), 200);
                                    }
                                    break;

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                            break;

                        case (preg_match('/score_\d+/', $minorClass) ? true : false):
                            // 각 성적별 분석
                            $explode = explode('_', $minorClass);
                            $scoreId = $explode[1];

                            // 그래프 유형에 따른 분석 결과 획득
                            switch($graphType) {
                                case 'histogram':
                                    // 히스토그램
                                    $score = Score::findOrFail($scoreId);
                                    $gainedScores = $score->gainedScores()->join('users', 'gained_scores.std_id', 'users.id');

                                    // 해당 성적의 만점/최소 취득점수/점수구간 설정
                                    $maxScore = $score->perfect_score;
                                    $minGainedScore = $gainedScores->min('score');
                                    $range = ceil(($maxScore - $minGainedScore) / 11);

                                    // 해당 성적에 대한 상세 데이터 삽입
                                    $result['subject']          = $score->subject->name;
                                    $result['name']             = $score->detail;
                                    $result['perfect_score']    = $score->perfect_score;

                                    // 구간별 취득점수 현황 계산
                                    for($scoreCount = $maxScore; $scoreCount >= $minGainedScore; $scoreCount -= ($range + 1)) {
                                        // 해당 점수
                                        $start  = $scoreCount;
                                        $end    = $scoreCount - $range > 0 ? $scoreCount - $range : 0;
                                        $query = with(clone $gainedScores)->maxScore($start)->minScore($end)
                                            ->select('gained_scores.std_id', 'users.name', 'gained_scores.score');

                                        // 조회 결과 설정
                                        $data['x-point']    = "{$start}~{$end}";
                                        $data['y-point']    = $query->count();
                                        $data['detail']     = $query->get()->all();

                                        $result['value'][]  = $data;
                                    }
                                    break;

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                            break;

                        default:
                            throw new NotValidatedException("지원하지 않는 소분류입니다.");
                    }
                    break;
            }



        } else {
            // 학생 분석
            switch ($majorClass) {
                case 'ada':
                    // 출석 분석
                    // 출석 기록 획득
                    $query = $student->attendances()->orderBy('attendances.reg_date');

                    // 소분류에 따른 알고리즘 변경
                    switch($minorClass) {
                        case 'sign_in':
                        case 'sign_out':
                            // 등/하교 관련
                            // 기준 있는 꺾은선 : 등/하교 시간 변화량
                            switch($graphType) {
                                case 'compare':
                                    // 등/하교 기준 조회
                                    $result['limit'] = $professor->studyClass->{"{$minorClass}_time"};

                                    // 각 기간별 평균 등/하교 시간량 변화
                                    for($dateCount = $startDate->copy(); $dateCount->lte($endDate); $dateCount->{"add{$ucPeriod}"}()) {
                                        // 기간 단위에 따른 휴일 제외 알고리즘 변경
                                        $holidayList = [];
                                        switch($periodType) {
                                            case 'daily':
                                                if($student->studyClass->isHolidayAtThisDay($dateCount->format('Y-m-d')) != false) {
                                                    continue 2;
                                                }
                                                break;
                                            case 'weekly':
                                            case 'recently':
                                            case 'monthly':
                                                $start  = $dateCount->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d');
                                                $end    = $dateCount->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d');
                                                $holidays = $student->studyClass->selectHolidaysList($start, $end);
                                                $holidayList = array_keys($holidays);
                                                break;
                                        }

                                        // 질의문 설정
                                        $queryResult = with(clone $query)
                                            ->start($dateCount->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'))
                                            ->end($dateCount->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d'))
                                            ->where('absence_flag', 'good')
                                            ->whereNotIn('reg_date', $holidayList);

                                        // 질의 결과 획득
                                        $time   = null;
                                        $detail = null;
                                        if($queryResult->exists()) {
                                            // 평균 등/하교 시간 구하기
                                            $time = with(clone $queryResult)
                                                ->select(
                                                    DB::raw("date_format(sec_to_time(avg(time_to_sec({$minorClass}_time))), '%H:%i:%s') as avg_time")
                                                )->pluck('avg_time')->first();

                                            // 상세 내역 구하기
                                            $selectList = ['reg_date', "{$minorClass}_time"];
                                            switch($minorClass) {
                                                case 'sign_in':
                                                    $selectList[] = 'lateness_flag';
                                                    break;
                                                case 'sign_out':
                                                    $selectList[] = 'early_leave_flag';
                                                    break;
                                            }
                                            $detail = with(clone $queryResult)->get($selectList)->all();
                                            foreach($detail as $key => $val) {
                                                if(isset($val->lateness_flag)) {
                                                    $detail[$key]->lateness_flag = __("ada.{$val->lateness_flag}");
                                                }

                                                if(isset($val->early_leave_flag)) {
                                                    $detail[$key]->early_leave_flag = __("ada.{$val->early_leave_flag}");
                                                }
                                            }
                                        }

                                        // 기간 단위에 따른 데이터 획득
                                        $data = ['x-point' => null, 'y-point' => $time, 'detail' => $detail];
                                        switch($periodType) {
                                            case 'daily':
                                                $data['x-point'] = $dateCount->format('Y-m-d');
                                                break;
                                            case 'weekly':
                                            case 'recently':
                                                $data['x-point'] = sprintf('%d-%02d-%d', $dateCount->year, $dateCount->month, $dateCount->weekOfMonth);
                                                break;
                                            case 'monthly':
                                                $data['x-point'] = $dateCount->format('Y-m');
                                                break;
                                        }

                                        $result['value'][] = $data;
                                    }
                                    break;

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                            break;



                        case 'lateness':
                        case 'early_leave':
                        case 'absence':
                            // 지각/결석/조퇴 관련
                            // 단일 꺾은선 : (지각|조퇴|결석) 횟수 변화량
                            switch($graphType) {
                                case 'single_line':
                                    // 각 기간별 지각/조퇴/결석 횟수 구하기
                                    for($dateCount = $startDate->copy(); $dateCount->lte($endDate); $dateCount->{"add{$ucPeriod}"}()) {
                                        $queryResult = with(clone $query)
                                            ->start($dateCount->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'))
                                            ->end($dateCount->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d'))
                                            ->{camel_case($minorClass)}();

                                        // 질의 결과 획득
                                        $data = ['x-point' => null, 'y-point' => 0, 'detail' => null];
                                        if($queryResult->exists()) {
                                            $data['y-point'] = with(clone $queryResult)->count();

                                            // 상세 내역
                                            $flag = "{$minorClass}_flag";
                                            $selectList = [$flag];
                                            switch($minorClass) {
                                                case 'lateness':
                                                    $selectList[] = 'sign_in_time';
                                                    break;
                                                case 'early_leave':
                                                    $selectList[] = 'sign_out_time';
                                                    break;
                                                case 'absence':
                                                    $selectList[] = 'reg_date';
                                                    break;
                                            }
                                            $detail = $queryResult->select($selectList)->get()->all();
                                            foreach($detail as $key => $val) {
                                                $detail[$key]->{$flag} = __("ada.{$val->{$flag}}");
                                            }

                                            $data['detail'] = $detail;
                                        }

                                        // 기간 단위에 따른 데이터 획득
                                        switch($periodType) {
                                            case 'weekly':
                                            case 'recently':
                                                $data['x-point'] = sprintf('%d-%02d-%d', $dateCount->year, $dateCount->month, $dateCount->weekOfMonth);
                                                break;
                                            case 'monthly':
                                                $data['x-point'] = $dateCount->format('Y-m');
                                                break;
                                        }

                                        $result['value'][] = $data;
                                    }
                                    break;

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                            break;



                        default:
                            // 그 이외
                            switch($graphType) {
                                case 'donut':
                                    // 도넛: 조회 기간동안의 등교 유형별 비율 + 조퇴 횟수
                                    // 그래프에 사용하는 기본정보 설정
                                    $query = with(clone $query)
                                        ->start($startDate->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'))
                                        ->end($endDate->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d'));

                                    $data['graph']['good'] = with(clone $query)->signInGood()->count();
                                    $data['graph']['lateness'] = with(clone $query)->lateness()->count();
                                    $data['graph']['absence'] = with(clone $query)->absence()->count();
                                    $data['graph']['early_leave'] = with(clone $query)->earlyLeave()->count();

                                    // 그래프 상세정보 설정
                                    foreach (['lateness', 'absence', 'early_leave'] as $type) {
                                        // 출석 유형에 따른 선택문 변경
                                        $select[] = 'detail';
                                        $select[] = "{$type}_flag";
                                        switch ($type) {
                                            case 'lateness':
                                                $select[] = 'sign_in_time';
                                                break;
                                            case 'early_leave':
                                                $select[] = 'sign_out_time';
                                                break;
                                            case 'absence':
                                                $select[] = 'reg_date';
                                                break;
                                        }
                                        $temp = with(clone $query)->{camel_case($type)}()
                                            ->select()->get($select)->all();

                                        // 각 데이터를 수정
                                        foreach ($temp as $key => $value) {
                                            switch ($type) {
                                                case 'lateness':
                                                    $temp[$key] = [
                                                        'sign_in_time' => $value->sign_in_time,
                                                        'lateness_flag' => __("ada.{$value->lateness_flag}"),
                                                    ];
                                                    break;
                                                case 'absence':
                                                    $temp[$key] = [
                                                        'reg_date'     => $value->reg_date,
                                                        'absence_flag' => __("ada.{$value->absence_flag}"),
                                                    ];
                                                    break;
                                                case 'early_leave':
                                                    $temp[$key] = [
                                                        'sign_out_time' => $value->sign_out_time,
                                                        'early_leave_flag' => __("ada.{$value->early_leave_flag}"),
                                                    ];
                                                    break;
                                            }
                                        }
                                        $data['detail'][$type] = $temp;
                                    }

                                    $result['value'] = $data;
                                    break;

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                    }
                    break;



                case 'study':
                    // 학업 분석

                    // 쿼리빌더 작성 시 사용할 기본 데이터 정의
                    $subjects   = [];           // 조회할 강의 목록
                    switch($minorClass) {
                        case 'japanese':
                        case 'major':
                            // 그래프 유형에 따른 결과값 도출
                            switch($graphType) {
                                case 'single_line':
                                    // 일본어|전공 평균 석차백분율 변화량
                                    // 지정된 기간에 포함된 학기 조회
                                    $terms = Term::termsIncludePeriod(
                                        $startDate->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'),
                                        $endDate->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d')
                                    )->get()->all();

                                    // 조회할 강의 정보 구하기
                                    foreach($terms as $term) {
                                        $joinSubject = $student->subjects()->where([['year', $term->year], ['term', $term->term]])
                                            ->{$minorClass}()->pluck('subjects.id')->all();
                                        $subjects = array_merge($subjects, $joinSubject);
                                    }
                                    break;

                                case 'double_line':
                                    // 일본어/전공 평균석차백분율 변화
                                    foreach(['japanese', 'major'] as $value) {
                                        // 강의 유형별 데이터 획득
                                        $data = $this->getDataOfGraph(new Request([
                                            'major_class'   => $majorClass,
                                            'minor_class'   => $value,
                                            'start_date'    => $request->has('start_date') ? $request->get('start_date') : null,
                                            'end_date'      => $request->has('end_date') ? $request->get('end_date') : null,
                                            'graph_type'    => 'single_line',
                                            'period_type'   => $periodType,
                                            'std_id'        => $student->id
                                        ]))->original->message;

                                        $result[$value] = $data;
                                    }

                                    // 결과값 반환
                                    return response()->json(new ResponseObject(
                                        true, $result
                                    ), 200);

                                default:
                                    throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                            }
                            break;

                        case (preg_match('/subject_\d+/', $minorClass) ? true : false):
                            // 해당 강의에 대한 성적 변화 그래프
                            $explode = explode('_', $minorClass);
                            array_push($subjects, $explode[1]);
                            break;

                        case (preg_match('/\d+_(homework|quiz|final|midterm)/', $minorClass) ? true : false):
                            // 성적에 대한 성적 변화 그래프
                            $explode    = explode('_', $minorClass);
                            $scoreTypeQuery = [
                                'id'    => $explode[0],
                                'type'  => $explode[1]
                            ];
                            array_push($subjects, $scoreTypeQuery['id']);
                            break;

                        default:
                            throw new NotValidatedException("지원하지 않는 소분류입니다.");
                    }

                    // 기간 단위에 따른 성적 데이터 도출
                    for ($dateCount = $startDate->copy(); $dateCount->lte($endDate); $dateCount->{"add{$ucPeriod}"}()) {
                        // 성적 유형 질의문 설정
                        $scoreQuery = Score::whereIn('subject_id', $subjects)
                            ->start($dateCount->copy()->{"startOf{$ucPeriod}"}()->format('Y-m-d'))
                            ->end($dateCount->copy()->{"endOf{$ucPeriod}"}()->format('Y-m-d'));


                        if (isset($scoreTypeQuery)) {
                            // 소분류에서 성적 분류를 지정했을 때
                            $scoreQuery = $scoreQuery->{$scoreTypeQuery['type']}()->pluck('id')->all();
                        } else {
                            // 이외의 경우
                            $scoreQuery = $scoreQuery->pluck('id')->all();
                        }



                        $query = $student->gainedScores()->whereIn('score_type', $scoreQuery);
                        $data = ['x-point' => null, 'y-point' => null];     // 데이터 표시점
                        // 그래프 유형에 따른 데이터 설정
                        switch ($graphType) {
                            case 'single_line':
                                // 각 기간별 석차백분율 도출
                                // 각 기간별 취득 성적목록 획득
                                if ($query->exists()) {
                                    $data['y-point'] = ceil($query->avg('standing_order') * 100);
                                    $detail = $query->join('scores', 'scores.id', 'gained_scores.score_type')
                                        ->orderBy('scores.execute_date')
                                        ->select(
                                            'scores.detail', 'scores.execute_date', 'scores.type',
                                            DB::raw('standing_order * 100 as standing_order')
                                        )->get()->all();

                                    // 다국어 언어팩 적용
                                    foreach($detail as $key => $val) {
                                        $detail[$key]->type = __("study.{$val->type}");
                                    }

                                    $data['detail'] = $detail;
                                }

                                // 결과값의 x-포인트 지정
                                switch ($periodType) {
                                    case 'monthly':
                                        $data['x-point'] = $dateCount->format('Y-m');
                                        break;
                                    case 'weekly':
                                    case 'recently':
                                        $data['x-point'] = sprintf('%d-%02d-%d', $dateCount->year, $dateCount->month, $dateCount->weekOfMonth);
                                        break;
                                }
                                $result['value'][] = $data;

                                break;

                            case 'double_line':
                                // 기간 단위에 따른 반 평균 대비 취득점수 그래프
                                foreach(['class_average', 'gained_score'] as $value) {
                                    if($value == 'gained_score') {
                                        // 해당 학생의 취득점수를 조회
                                        if ($query->exists()) {
                                            $queryResult = with(clone $query)->join('scores', 'scores.id', 'gained_scores.score_type')
                                                ->orderBy('scores.execute_date')
                                                ->select(
                                                    'scores.detail', 'scores.execute_date', 'scores.type',
                                                    'gained_scores.score', 'scores.perfect_score'
                                                );
                                            $detail = $queryResult->get()->all();

                                            // 다국어 언어팩 적용
                                            foreach($detail as $key => $val) {
                                                $detail[$key]->type = __("study.{$val->type}");
                                            }

                                            $data['y-point'] = ceil(($queryResult->sum('score') / $queryResult->sum('perfect_score')) * 100);
                                            $data['detail'] = $detail;
                                        }
                                    } else {
                                        // 해당학생의 소속반 평균 점수를 조회
                                        if ($query->exists()) {
                                            $queryResult = Score::whereIn('id', $scoreQuery)->orderBy('scores.execute_date')
                                                ->select(
                                                    'scores.detail', 'scores.execute_date', 'scores.type',
                                                    'scores.average_score', 'scores.perfect_score'
                                                );

                                            $detail = $queryResult->get()->all();

                                            // 다국어 언어팩 적용
                                            foreach($detail as $key => $val) {
                                                $detail[$key]->type = __("study.{$val->type}");
                                            }

                                            $data['y-point'] = ceil(($queryResult->sum('average_score') / $queryResult->sum('perfect_score')) * 100);
                                            $data['detail'] = $detail;
                                        }
                                    }

                                    // 결과값의 x-포인트 지정
                                    switch ($periodType) {
                                        case 'monthly':
                                            $data['x-point'] = $dateCount->format('Y-m');
                                            break;
                                        case 'weekly':
                                        case 'recently':
                                            $data['x-point'] = sprintf('%d-%02d-%d', $dateCount->year, $dateCount->month, $dateCount->weekOfMonth);
                                            break;
                                    }
                                    $result['value'][$value][] = $data;
                                }
                                break;

                            default:
                                throw new NotValidatedException("지원하지 않는 그래프 유형입니다.");
                        }
                    }
                    break;
            }
        }

        // 04. 결과 반환
        return response()->json(new ResponseObject(
            true, $result
        ), 200);
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
        $students   = $professor->students()->get()->all();
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

    // 지도반 강의 관리
    public function selectSubjectsList(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'term'  => ['regex:/(19|20)\d{2}-((1st|2nd)_term|(summer|winter)_vacation)/']
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $term       = $this->getTermValue($request->get('term'));
        $subjects   = $professor->studyClass->subjects()->term($term['this'])
            ->orderBy('name');

        // 03. 반환 데이터
        $data = [
            'subjects'      => $subjects->get()->all(),
            'pagination'    => [
                'this'  => $term['this_format'],
                'prev'  => $term['prev'],
                'next'  => $term['next']
            ]
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    // 학생별 상세 관리

    // 해당 학생의 관심도 설정
    public function setAttentionLevel(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'std_id'                => 'required|exists:students,id',
            'attention_level'       => 'required|numeric|min:0|max:3',
//            'attention_reason'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get("user")->id);
        $student    = $professor->isMyStudent($request->post('std_id'));

        $query = ['attention_level' => $request->post('attention_level')];

        // 03. 갱신
        if($student->update($query)) {
            return response()->json(new ResponseObject(
                true, __('response.update_success', ['element' => __('tutor.attention_level')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response.update_failed', ['element' => __('tutor.attention_level')])
            ), 200);
        }
    }

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

    // 지도반 일정 관리

    // 일정 조회
    public function selectSchedule(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $professor  = Professor::findOrFail(session()->get('user')->id);
        $startDate  = Carbon::parse($request->get('start_date'));
        $endDate    = Carbon::parse($request->get('end_date'));
        $schedule   = Schedule::selectBetweenDate($startDate->format("Y-m-d"), $endDate->format('Y-m-d'))
            ->whereNull('class_id')->orWhere('class_id', $professor->studyClass->id)
            ->orderBy('start_date')->get()->all();

        return response()->json(new ResponseObject(
            true, $schedule
        ), 200);
    }

    // 일정 삽입
    public function insertSchedule(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'name'              => 'required|string|min:2',
            'holiday_flag'      => 'required|boolean',
            'include_flag'      => 'required_if:holiday_flag,0,false|boolean',
            'in_default_flag'   => 'required_if:holiday_flag,0,false|boolean',
            'out_default_flag'  => 'required_if:holiday_flag,0,false|boolean',
//            'sign_in_time'      => "required_if:holiday_flag,0,false|date_format:H:i:s",
//            'sign_out_time'     => "required_if:holiday_flag,0,false|date_format:H:i:s|after_or_equal:sign_in_time",
            'contents'          => 'string'
        ]);

        // 휴일 플래그가 false 이고 등교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 등교시간을 지정
        $validator->sometimes('sign_in_time', 'required_if:in_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });

        // 휴일 플래그가 false 이고 하교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 하교 시간을 지정
        $validator->sometimes('sign_out_time', 'required_if:out_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });


        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studyClass     = Professor::findOrFail(session()->get('user')->id)->studyClass;
        $startDate      = Carbon::parse($request->post('start_date'));
        $endDate        = Carbon::parse($request->post('end_date'));
        $name           = $request->post('name');
        $holidayFlag    = $request->post('holiday_flag');
        $includeFlag    = true;
        $signInTime     = null;
        $signOutTime    = null;
        $contents       = $request->has('contents') ? $request->post('contents') : '';

        // 시간 데이터 획득
        if(!$holidayFlag) {
            // 등교 시각
            if(!$request->post('in_default_flag')) {
                $signInTime = Carbon::parse($request->post("sign_in_time"));
            }

            // 하교 시각
            if(!$request->post('out_default_flag')) {
                $signOutTime = Carbon::parse($request->post('sign_out_time'));
            }

            $includeFlag = $request->post('include_flag');
        }

        // 지정된 기간동안 이미 정의된 일정이 있을 경우 => 삽입 거부
        if(Schedule::selectBetweenDate($startDate->format("Y-m-d"), $endDate->format('Y-m-d'))->common()->exists()) {
            throw new NotValidatedException("지정 기간 이내에 이미 일정이 존재합니다.");
        }

        // 03. 스케쥴 등록
        $setData = [
            'start_date'        => $startDate->format('Y-m-d'),
            'end_date'          => $endDate->format('Y-m-d'),
            'name'              => $name,
            'type'              => Schedule::TYPE['class'],
            'class_id'          => $studyClass->id,
            'holiday_flag'      => $holidayFlag,
            'include_flag'      => $includeFlag,
            'sign_in_time'      => is_null($signInTime) ? $signInTime : $signInTime->format('H:i:s'),
            'sign_out_time'     => is_null($signOutTime) ? $signOutTime : $signOutTime->format('H:i:s'),
            'contents'          => $contents
        ];

        if(Schedule::insert($setData)) {
            // 일정 등록 성공 => 성공 메시지 반환
            return response()->json(new ResponseObject(
                true, __('response.insert_success', ['element' => __('ada.schedule_class')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response.insert_failed', ['element' => __('ada.schedule_class')])
            ), 200);
        }
    }

    // 일정 갱신
    public function updateSchedule(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'id'                => 'required|exists:schedules,id',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'name'              => 'required|string|min:2',
            'holiday_flag'      => 'required|boolean',
            'include_flag'      => 'required_if:holiday_flag,0,false|boolean',
            'in_default_flag'   => 'required_if:holiday_flag,0,false|boolean',
            'out_default_flag'   => 'required_if:holiday_flag,0,false|boolean',
//            'sign_in_time'      => "required_if:holiday_flag,0,false|date_format:H:i:s",
//            'sign_out_time'     => "required_if:holiday_flag,0,false|date_format:H:i:s|after_or_equal:sign_in_time",
            'contents'          => 'string'
        ]);

        // 휴일 플래그가 false 이고 등교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 등교시간을 지정
        $validator->sometimes('sign_in_time', 'required_if:in_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });

        // 휴일 플래그가 false 이고 하교시각 기본값 플래그가 false 일 때 -> 관리자가 직접 하교 시간을 지정
        $validator->sometimes('sign_out_time', 'required_if:out_default_flag,0,false|date_format:H:i:s', function($input) {
            return !$input->holiday_flag;
        });

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $schedule       = Schedule::findOrFail($request->post('id'));
        if(!$schedule->classCheck(Professor::findOrFail(session()->get('user')->id)->studyClass->id)) {
            // 일정 유형이 내 지도반 코드가 아닌 경우 => 데이터에 대한 접근 거부
            throw new NotValidatedException('해당 데이터에 접근할 권한이 없습니다.');
        }

        $startDate      = Carbon::parse($request->post('start_date'));
        $endDate        = Carbon::parse($request->post('end_date'));
        $name           = $request->post('name');
        $holidayFlag    = $request->post('holiday_flag');
        $includeFlag    = true;
        $signInTime     = null;
        $signOutTime    = null;
        $contents       = $request->has('contents') ? $request->post('contents') : '';

        // 지정된 기간동안 이미 정의된 일정이 있을 경우 => 수정 거부
        if(Schedule::selectBetweenDate($startDate->format("Y-m-d"), $endDate->format('Y-m-d'))->
        common()->where('id', '!=', $schedule->id)->exists()) {
            throw new NotValidatedException("지정 기간 이내에 이미 일정이 존재합니다.");
        }

        // 시간 데이터 획득
        if(!$holidayFlag) {
            // 등교 시각
            if(!$request->post('in_default_flag')) {
                $signInTime = Carbon::parse($request->post("sign_in_time"));
            }

            // 하교 시각
            if(!$request->post('out_default_flag')) {
                $signOutTime = Carbon::parse($request->post('sign_out_time'));
            }

            $includeFlag = $request->post('include_flag');
        }


        // 03. 일정 갱신
        $setData = [
            'start_date'        => $startDate->format('Y-m-d'),
            'end_date'          => $endDate->format('Y-m-d'),
            'name'              => $name,
            'holiday_flag'      => $holidayFlag,
            'include_flag'      => $includeFlag,
            'sign_in_time'      => is_null($signInTime) ? $signInTime : $signInTime->format('H:i:s'),
            'sign_out_time'     => is_null($signOutTime) ? $signOutTime : $signOutTime->format('H:i:s'),
            'contents'          => $contents
        ];

        if($schedule->update($setData)) {
            // 갱신 성공
            return response()->json(new ResponseObject(
                true, __('response.update_success', ['element' => __('ada.schedule_class')])
            ), 200);
        } else {
            // 갱신 실패
            return response()->json(new ResponseObject(
                false, __('response.update_failed', ['element' => __('ada.schedule_class')])
            ), 200);
        }
    }

    // 일정 삭제
    public function deleteSchedule(Request $request) {
    // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:schedules,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $schedule = Schedule::findOrFail($request->post('id'));
        if(!$schedule->classCheck(Professor::findOrFail(session()->get("user")->id)->studyClass->id)) {
            throw new NotValidatedException('해당 데이터에 대한 접근권한이 없습니다.');
        }

        // 03. 일정 삭제
        if($schedule->delete()) {
            // 갱신 성공
            return response()->json(new ResponseObject(
                true, __('response.delete_success', ['element' => __('ada.schedule_class')])
            ), 200);
        } else {
            // 갱신 실패
            return response()->json(new ResponseObject(
                false, __('response.delete_failed', ['element' => __('ada.schedule_class')])
            ), 200);
        }
    }
}
