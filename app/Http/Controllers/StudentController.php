<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Student;
use App\Exceptions\NotValidatedException;

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
            'period'    => 'in:weekly,monthly',
            'date'      => 'required_with:period|regex:/^[1-2]\d{3}-\d{1,2}$/'
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
                $pagination = [
                    'prev'  => "{$date['prev']->year}-{$date['prev']->weekOfYear}",
                    'this'  => "{$date['this']->year}년 {$date['this']->month}월 {$date['this']->weekOfMonth}주차",
                    'next'  => is_null($date['next']) ? null : "{$date['next']->year}-{$date['next']->weekOfYear}",
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
                    'this'  => "{$date['this']->year}년 {$date['this']->month}월",
                    'next'  => is_null($nextMonth) ? null : "{$date['next']->year}-{$nextMonth}",
                ];
                break;
        }

        ##### 조회 결과가 없을 경우 #####
        if(with(clone $attendances)->count() <= 0) {
            return response()->json(new ResponseObject(
                false, "조회된 출석기록이 없습니다."
            ));
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
            'attendance_rate'       => number_format(($signIn / with(clone $attendances)->count()), 2) * 100,

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
            // 과목별 성적 목록
            $subject->scores    = $student->selectScoresList($subject->id)->get()->all();

            // 성적 통계표
            $finalStats     = $student->selectStatsOfType($subject->id)->where('type', 'final')->get()->all();
            $midtermStats   = $student->selectStatsOfType($subject->id)->where('type', 'midterm')->get()->all();
            $homeworkStats  = $student->selectStatsOfType($subject->id)->where('type', 'homework')->get()->all();
            $quizStats      = $student->selectStatsOfType($subject->id)->where('type', 'quiz')->get()->all();
            $subject->stats     = [
                'final'     => [
                    'type'          => '기말',
                    'count'         => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->count,
                    'perfect_score' => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->perfect_score,
                    'gained_score'  => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->gained_score,
                    'average'       => sizeof($finalStats) <= 0 ? 0 : $finalStats[0]->average,
                    'reflection'    => sprintf("%02d", $subject->final_reflection * 100)
                ],
                'midterm'   => [
                    'type'          => '중간',
                    'count'         => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->count,
                    'perfect_score' => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->perfect_score,
                    'gained_score'  => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->gained_score,
                    'average'       => sizeof($midtermStats) <= 0 ? 0 : $midtermStats[0]->average,
                    'reflection'    => sprintf("%02d", $subject->midterm_reflection * 100)
                ],
                'homework'  => [
                    'type'          => '과제',
                    'count'         => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->count,
                    'perfect_score' => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->perfect_score,
                    'gained_score'  => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->gained_score,
                    'average'       => sizeof($homeworkStats) <= 0 ? 0 : $homeworkStats[0]->average,
                    'reflection'    => sprintf("%02d", $subject->homework_reflection * 100)
                ],
                'quiz'      => [
                    'type'          => '쪽지',
                    'count'         => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->count,
                    'perfect_score' => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->perfect_score,
                    'gained_score'  => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->gained_score,
                    'average'       => sizeof($quizStats) <= 0 ? 0 : $quizStats[0]->average,
                    'reflection'    => sprintf("%02d", $subject->quiz_reflection * 100)
                ],
            ];

            // 학업성취도 계산
            $achievement = [];
            foreach($subject->stats as $stat) {
                array_push($achievement, $stat['average'] * $stat['reflection']);
            }
            $subject->achievement = sprintf("%02d", array_sum($achievement));
        }

        // 05. 페이지네이션 데이터 설정
        $thisTermFormat = null;
        switch(($thisTerm = explode('-', $period['this']))[1]) {
            case '1st_term':
                $thisTermFormat = "{$thisTerm[0]}년도 1학기";
                break;
            case 'summer_vacation':
                $thisTermFormat = "{$thisTerm[0]}년도 여름방학";
                break;
            case '2nd_term':
                $thisTermFormat = "{$thisTerm[0]}년도 2학기";
                break;
            case 'winter_vacation':
                $thisTermFormat = "{$thisTerm[0]}년도 겨울방학";
                break;
        }
        $pagination = [
            'prev'  => $period['prev'],
            'this'  => $thisTermFormat,
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
