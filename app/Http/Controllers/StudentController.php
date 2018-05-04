<?php

namespace App\Http\Controllers;

use Dotenv\Exception\ValidationException;
use Validator;
use Illuminate\Http\Request;
use App\Student;
use App\Attendance;
use App\Exceptions\NotValidatedException;
use Illuminate\Support\Carbon;

/**
 *  클래스명:               StudentController
 *  설명:                   학생 회원에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  메서드 목록
 *  - 메인
 *      = index()
 *          : 학생 회원의 메인 페이지를 출력
 *
 *
 *
 *  - 내 정보 관리
 *
 *
 *
 *  - 출결정보
 *      = getMyAttendanceRecords(Request $request)
 *          : 자신의 최근 출석 데이터를 조회
 *
 *
 *
 *  - 학업정보
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
    /**
     *  함수명:                         getMyAttendanceRecords
     *  함수 설명:                      자신의 최근 출석 데이터를 조회
     *  만든날:                         2018년 4월 28일
     *
     *  매개변수 목록
     * @param Request $request :        요청 메시지
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     * @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     * @throws NotValidatedException
     */
    public function getMyAttendanceRecords(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            // 요청에 필요한 값은 period(조회기간 단위), date(조회기간)
            'period'    => 'required_with:date|in:weekly,monthly',
            'date'      => 'regex:/^[1-2]\d{3}-\d{2}$/'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        // 요청받은 조회기간이 없다면 => 현재 주간을 기준으로 조회
        $argPeriod  = $request->exists('period') ? $request->get('period') : 'weekly';
        $argDate    = $request->exists('date') ? $request->get('date') : null;
        $student    = Student::findOrFail(session()->get('user')->id);

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

        ##### 조회 결과가 없을 경우 #####
        if(with(clone $attendances)->count() <= 0) {
            return response()->json(new ResponseObject(
                false, "조회된 출석기록이 없습니다."
            ), 200);
        }


        // 04. 데이터 조회결과 반환
        $data = [
            // 정상 등교
            'sign_in'               => $signIn = with(clone $attendances)->signIn()->count('reg_date'),

            // 지각
            'lateness'              => with(clone $attendances)->lateness()->count('reg_date'),

            // 조퇴
            'early_leave'           => with(clone $attendances)->earlyLeave()->count('reg_date'),

            // 결석
            'absence'               => with(clone $attendances)->absence()->count('reg_date'),

            // 출석률 (정상 등교 / 총 출석일)
            'attendance_rate'       => number_format(($signIn / with(clone $attendances)->count()) * 100, 0),

            // 최근 정상등교 일자
            'recent_sign_in'        => with(clone $attendances)->signIn()->max('reg_date'),

            // 최근 지각 일자
            'recent_lateness'       => with(clone $attendances)->lateness()->max('reg_date'),

            // 최근 조퇴 일자
            'recent_early_leave'    => with(clone $attendances)->earlyLeave()->max('reg_date'),

            // 최근 결석 일자
            'recent_absence'        => with(clone $attendances)->absence()->max('reg_date'),

            // 페이지네이션용 데이터
            'pagination'            => $pagination
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }

    // 하드웨어 : 등교
    public function signInOfHardware(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'student_number_id'     => 'required|exists:students,id',
        ]);

        if($validator->fails()) {
            throw new ValidationException("허가되지 않은 접근입니다.");
        }

        // 02. 데이터 획득
        $student        = Student::findOrFail($request->post('student_number_id'));
        $detail         = $request->exists('detail') ? $request->post('detail') : "";
        $signInTime     = Carbon::create();

        // 03. 심층 유효성 검사
        // 오늘자 출석기록 조회
        $adaRecordOfToday = $student->attendances()->start($signInTime->format('Y-m-d'))
                                ->end($signInTime->format('Y-m-d'))->get()->all();
        if(sizeof($adaRecordOfToday) > 0) {
            // 오늘의 출석기록이 있으면 => 출석 인증 중단
            return response()->json(new ResponseObject(
                false, "오늘은 이미 출석하셨습니다."
            ), 200);
        }

        // 04. 출석
        $signInLimit    = explode(':', $student->studyClass->sign_in_time);
        $attendance = new Attendance();
        $attendance->std_id             = $student->id;
        $attendance->reg_date           = $signInTime->format('Y-m-d');
        $attendance->sign_in_time       = $signInTime->format('Y-m-d H:i:s');
        $attendance->sign_out_time      = null;
        $attendance->lateness_flag      = $signInTime->gt(Carbon::createFromTime($signInLimit[0], $signInLimit[1], $signInLimit[2]))
                                    ? 'unreason' : 'good';
        $attendance->early_leave_flag   = 'good';
        $attendance->absence_flag       = 'good';
        $attendance->detail             = $detail;

        // 05. 결과 반환
        if($attendance->save()) {
            return response()->json(new ResponseObject(
                true, "좋은 아침입니다, {$student->user->name}님."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "출석 인증에 실패하였습니다."
            ), 200);
        }
    }

    // 하드웨어 : 하교


    // 학업 정보
    /**
     *  함수명:                         getMySubjectList
     *  함수 설명:                      학생이 수강하는 강의들에 대하여 상세한 성적 정보를 조회
     *  만든날:                         2018년 4월 30일
     *
     *  매개변수 목록
     * @param Request $request :        요청 메시지
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     * @return \Illuminate\Http\JsonResponse 예외
     *
     * 예외
     * @throws NotValidatedException
     */
    public function getMySubjectList(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'period'  => 'regex:/^[1-2]\d{3}-[1-2]?[a-zA-Z_]+$/'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException();
        }

        // 02. 데이터 획득
        // 요청받은 조회기간이 없다면 => 현재 학기를 기준으로 조회
        $student    = Student::findOrFail(session()->get('user')->id);
        $argPeriod  = $request->exists('period') ? $request->get('period') : null;

        // 03. 강의 데이터 조회
        $period     = $this->getTermValue($argPeriod);
        $subjects   = $student->selectSubjectsList($period['this'])->get()->all();

        // ##### 조회된 과목정보가 없을 때 ######
        if(sizeof($subjects) <= 0) {
            return response()->json(new ResponseObject(
                false, "해당 학기에 수강한 강의가 존재하지 않습니다."
            ));
        }

        // 04. 각 강의별 성취도 & 성적 데이터 조회
        foreach($subjects as $subject) {
            // 성적 통계 조회
            $statList = $student->selectStatList($subject->id);

            // 과목별 성적 목록 첨부
            $subject->scores    = $student->selectScoresList($subject->id)->get()->all();

            // 성적 통계표 첨부
            $subject->stats = $statList['stats'];

            // 학업성취도 첨부
            $subject->achievement = $statList['achievement'];
        }

        // 05. 페이지네이션 데이터 설정
        $pagination = [
            'prev'  => $period['prev'],
            'this'  => $period['this_format'],
            'next'  => $period['next']
        ];

        // 05. 데이터 조회 결과 반환
        $data = [
            // 각 과목별 상세 정보
            'subjects'      => $subjects,

            // 페이지네이션 정보
            'pagination'    => $pagination
        ];

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }
}
