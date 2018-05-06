<?php

namespace App\Http\Controllers;

use Dotenv\Exception\ValidationException;
use Validator;
use Illuminate\Http\Request;
use App\Student;
use App\Attendance;
use App\Exceptions\NotValidatedException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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
 *          = getMyInfo:                        사용자 정보를 획득
 *
 *          = updateMyInfo:                     사용자 정보를 갱신
 *
 *
 *
 *  - 출결정보
 *          = getMyAttendanceRecords:           자신의 최근 출석 데이터를 조회
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

    // 내 정보 관리
    /**
     *  함수명:                         getMyInfo
     *  함수 설명:                      사용자 정보를 획득
     *  만든날:                         2018년 5월 05일
     *
     *  매개변수 목록
     *  null
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse
     */
    public function getMyInfo() {
        return response()->json(new ResponseObject(
            true, [
                'id'        => session()->get('user')->id,
                'name'      => session()->get('user')->name,
                'phone'     => session()->get('user')->phone,
                'email'     => session()->get('user')->email,
                'photo'     => session()->get('user')->photo_url
            ]
        ), 200);
    }

    /**
     *  함수명:                         updateMyInfo
     *  함수 설명:                      사용자 정보를 수정
     *  만든날:                         2018년 5월 05일
     *
     *  매개변수 목록
     *  @param Request $request:        요청 메시지
     *
     *  지역변수 목록
     *  null
     *
     *  반환값
     *  @return \Illuminate\Http\JsonResponse
     *
     *  예외
     *  @throws NotValidatedException
     */
    public function updateMyInfo(Request $request) {
        // 01. 유효성 검사
        $validator = Validator::make($request->all(), [
            'password'          => 'required_with:password_check|same:password_check',
            'password_check'    => 'required_with:password|same:password',
            'phone'             => 'required',
            'email'             => 'required|email',
            'photo'             => 'image'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $student    = Student::findOrFail(session()->get('user')->id);
        $password   = $request->post('password');
        $phone      = $request->post('phone');
        $email      = $request->post('email');
        $photo      = $request->hasFile('photo') ? $request->file('photo') : null;

        // 03. 데이터 수정
        $updateInfo['password'] = $password;
        $updateInfo['phone']    = $phone;
        $updateInfo['email']    = $email;

        if(!is_null($photo)) {
            // 기존 이미지가 존재한다면 => 기존 이미지 삭제
            $original_photo = session()->get('user')->photo;
                // 파일의 존재여부를 확인
            if(Storage::disk('std_photo')->exists($original_photo)) {
                // 삭제
                Storage::disk('std_photo')->delete($original_photo);
            }

            // 새 이미지 저장 => DB 사용자 정보에 새로운 이미지 경로를 지정
            $fileName = $photo->store('/','std_photo');
            $updateInfo['photo'] = $fileName;
        }

        // 04. 데이터 갱신
        if($student->updateMyInfo($updateInfo)) {
            session()->put('user', $student->user->selectUserInfo());

            return response()->json(new ResponseObject(
                true, "정보 갱신을 완료했습니다."
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "정보 갱신을 실패했습니다."
            ), 200);
        }
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

    // 등교
    public function signIn(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'student_number_id'     => 'exists:students,id',
            'detail'                => 'string|min:2'
        ]);

        if($validator->fails()) {
            throw new ValidationException("허가되지 않은 접근입니다.");
        }

        // 02. 데이터 획득
        $student        = $request->exists('student_number_id') ?
            Student::findOrFail($request->post('student_number_id')) :
            Student::findOrFail(session()->get('user')->id);
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

        // 상세 내역은 json encode
        $attendance->detail             = json_encode(new class($detail){
            public $sign_in;
            public function __construct($detail) {
                $this->sign_in = $detail;
            }
        });

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
    public function signOut(Request $request) {
        // 01. 요청 유효성 검사
        $validator = Validator::make($request->all(), [
            'student_number_id'     => 'exists:students,id',
            'detail'                => 'string|min:2'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException("허가되지 않은 접근입니다.");
        }

        // 02. 데이터 획득
        $student        = $request->exists('student_number_id') ?
            Student::findOrFail($request->post('student_number_id')) :
            Student::findOrFail(session()->get('user')->id);
        $detail         = $request->exists('detail') ? $request->post('detail') : "";
        $signOutTime    = Carbon::create();

        // 03. 심층 유효성 검사
        // 최근 데이터에 하교 데이터가 기록되었다면 => 하교 인증 실패
        $adaRecordOfRecent = $student->attendances()->orderDesc()->limit(1)->get()->all();
        if(sizeof($adaRecordOfRecent) <= 0 || !is_null($adaRecordOfRecent[0]->sign_out_time)) {
            return response()->json(new ResponseObject(
                false, "등교 내역이 없습니다."
            ), 200);
        }

        // 04. 하교
        $signOutLimit   = explode(':', $student->studyClass->sign_out_time);
        $attendance = $adaRecordOfRecent[0];
        $attendance->sign_out_time      = null;
        $attendance->early_leave_flag   = $signOutTime->lt(Carbon::createFromTime($signOutLimit[0], $signOutLimit[1], $signOutLimit[2]))
            ? 'unreason' : 'good';

        // 상세 사항을 json 객체로 입력
        $detailObject                   = json_decode($attendance->detail);
        $detailObject->sign_out         = $detail;
        $attendance->detail             = json_encode($detailObject);

        // 05. 결과 반환
        if($attendance->save()) {
            return response()->json(new ResponseObject(
                true, "고생하셨습니다, {$student->user->name}님."
            ), 200);#
        } else {
            return response()->json(new ResponseObject(
                false, "출석 인증에 실패하였습니다."
            ), 200);
        }
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
            // 과목별 성적 목록 첨부
            $subject->scores    = $student->selectScoresList($subject->id)->get()->all();

            // 성적 통계표 첨부
            $subject->stats = $student->selectStatList($subject->id);;

            // 학업성취도 첨부
            $subject->achievement =
                number_format($student->joinLists()->subject($subject->id)->get()[0]->achievement * 100, 0);
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
