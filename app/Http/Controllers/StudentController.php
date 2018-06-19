<?php

namespace App\Http\Controllers;

use App\Professor;
use Dotenv\Exception\ValidationException;
use PharIo\Manifest\RequiresElementTest;
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
        // 학생 학번을 전송받았을 경우 => 해당 학번으로 출석, 학번이 없으면 => 현재 로그인한 사용자로 출석
        $student        = $request->exists('student_number_id') ?
            Student::findOrFail($request->post('student_number_id')) :
            Student::findOrFail(session()->get('user')->id);
        $studyClass     = $student->studyClass;

        $detail         = $request->exists('detail') ? $request->post('detail') : "";
        $signInLimit    = Carbon::createFromTimeString($studyClass->sign_in_time);
        $signInTime     = now();
        $today          = today()->format('Y-m-d');

        // 03. 심층 유효성 검사
        // 오늘자 출석기록 조회
        $adaRecordOfToday = $student->attendances()->whereDate('reg_date', $today);
        if($adaRecordOfToday->exists()) {
            // 오늘의 출석기록이 있으면 => 출석 인증 중단
            return response()->json(new ResponseObject(
                false, "오늘은 이미 출석하셨습니다."
            ), 200);
        }

        // 04. 출석
        $latenessFlag   = 'good';
        if($studyClass->isHolidayAtThisDay($today) == false) {
            // 휴일이 아닌 경우

            if(($result = $studyClass->selectTodaySchedule($today)) != false) {
                // 오늘자의 특별한 일정이 존재하는 경우 => 해당 일정의 등교 시간대로 실행

                if(!is_null($result->sign_in_time)) {
                    // 등교시간이 지정된 경우 => 등교 제한시간을 변경
                    $signInLimit = Carbon::createFromTimeString($result->sign_in_time);
                }

            }
            // 일정이 존재하지 않는경우 => 기본 등교 시간대로 진행
            $latenessFlag = $signInTime->gt($signInLimit) ? 'unreason' : $latenessFlag;
        }
        $signInStart    = $signInLimit->copy()->subMinutes(30);

        // 자정 12시부터 지도교수 지정 출석시간의 30분 전까지는 출석 금지 시간
        if($signInTime->lt($signInStart)) {
            return response()->json(new ResponseObject(
                false, "자정부터 출석시간 30분 이전까지는 등교할 수 없습니다."
            ), 200);
        }

        // 메시지 객체 추가
        $detailObj = new class($detail){
            public $sign_in_message;
            public function __construct($detail) {
                $this->sign_in_message = $detail;
            }
        };
        if(isset($latenessTime)) {
            $detailObj->lateness_time = $latenessTime;
            unset($latenessTime);
        }

        $attendance = new Attendance();
        $attendance->std_id             = $student->id;
        $attendance->reg_date           = $signInTime->format('Y-m-d');
        $attendance->sign_in_time       = $signInTime->format('Y-m-d H:i:s');
        $attendance->sign_out_time      = null;
        $attendance->lateness_flag      = $latenessFlag;
        $attendance->early_leave_flag   = 'good';
        $attendance->absence_flag       = 'good';

        // 상세 내역은 json encode
        $attendance->detail             = json_encode($detailObj);

        // 05. 결과 반환
        if($attendance->save()) {
            return response()->json(new ResponseObject(
                true, [
                    'id'    => $student->id,
                    'name'  => $student->user->name,
                    'photo' => $student->user->selectUserInfo()->photo_url,
                    'time'  => $signInTime->format("Y-m-d H:i:s"),
                    'type'  => $latenessFlag == 'good' ? __('ada.good') : __('ada.lateness'),
                ]
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, "출석 인증에 실패하였습니다."
            ), 200);
        }
    }

    // 하교
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
        $studyClass     = $student->studyClass;
        $detail         = $request->exists('detail') ? $request->post('detail') : "";
        $signOutTime    = now();

        // 03. 심층 유효성 검사
        $adaRecord = $student->attendances()->orderDesc();

        $adaRecordOfRecent = null;
        if($adaRecord->exists()) {
            // 등교 이력이 존재하면 => 가장 최근의 등교 이력을 조회
            $adaRecordOfRecent = $adaRecord->first();

            if(!is_null($adaRecordOfRecent->sign_out_time)) {
                // 최근 데이터에 하교 데이터가 기록되었다면 => 하교 인증 실패
                return response()->json(new ResponseObject(
                    false, __('response.sign_out_error_no_sign_in', ['sign_out_time' => $adaRecordOfRecent->sign_out_time])
                ), 200);
            }

        } else {
            // 등교 이력이 없다면 => 하교 인증 실패
            return response()->json(new ResponseObject(
                false, __('response.sign_out_error_no_data')
            ), 200);
        }


        // 04. 하교

        // 조퇴 판단용 데이터 : 등교 일자, 하교 제한시각 획득
        $signInDate     = Carbon::parse($adaRecordOfRecent->reg_date);
        $signOutLimit   = $signInDate->copy()->setTimeFromTimeString($studyClass->sign_out_time);

        $earlyLeaveFlag = 'good';
        if($studyClass->isHolidayAtThisDay($signInDate->format('Y-m-d')) == false) {
            // 휴일이 아닌 경우

            if(($result = $studyClass->selectTodaySchedule($signInDate->format('Y-m-d'))) != false) {
                // 등교일자에 특별한 일정이 존재하는 경우 => 해당 일정의 하교 시간대로 실행

                if(!is_null($result->sign_out_time)) {
                    // 하교시간이 지정된 경우 => 하교 제한시간을 변경
                    $signOutLimit = $signOutLimit->setTimeFromTimeString($result->sign_out_time);
                }

            }
            // 일정이 존재하지 않는경우 => 기본 하교 시간대로 진행
            $earlyLeaveFlag = $signOutTime->lt($signOutLimit) ? 'unreason' : $earlyLeaveFlag;
        }

        $adaRecordOfRecent->sign_out_time      = $signOutTime->format('Y-m-d H:i:s');
        $adaRecordOfRecent->early_leave_flag   = $earlyLeaveFlag;

        // 상세 사항을 json 객체로 입력
        $detailObject                   = json_decode($adaRecordOfRecent->detail);
        $detailObject->sign_out_message = $detail;
        if(isset($earlyLeaveTime)) {
            $detailObject->early_leave_time = $earlyLeaveTime;
            unset($earlyLeaveTime);
        }

        $adaRecordOfRecent->detail             = json_encode($detailObject);

        // 05. 결과 반환
        if($adaRecordOfRecent->save()) {
            return response()->json(new ResponseObject(
                true, [
                    'id'    => $student->id,
                    'name'  => $student->user->name,
                    'photo' => $student->user->selectUserInfo()->photo_url,
                    'time'  => $signOutTime->format("Y-m-d H:i:s"),
                    'type'  => $earlyLeaveFlag == 'good' ? __('ada.good') : __('ada.early_leave'),
                ]
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response.sign_out_error_etc')
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
        $subjects   = $student->selectSubjectsList($period['this']);

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
            $subject->stats = $student->selectStatList($subject->id);

            /*
            // 학업성취도 첨부
            $subject->achievement =
                number_format($student->joinLists()->subject($subject->id)->get()[0]->achievement * 100, 0);
            */
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

    // 시간표 획득
    public function getTimetable() {
        // 01. 데이터 설정
        $student    = Student::findOrFail(session()->get('user')->id);
        $subjects   = $student->subjects()->term($this->getTermValue()['this'])->get()->all();

        // 02. 시간표 데이터 획득
        $timetables = [];
        foreach($subjects as $subject) {
            $temp = $subject->timetables->all();

            // 각 교시별 강의명 & 담당교수명 획득
            foreach($temp as $item) {
                $item->subject_name = $subject->name;
                $item->prof_name    = Professor::findOrFail($subject->professor)->user->name;
                unset($item->id);
                unset($item->subject_id);
            }

            // 획득한 데이터를 시간표 배열에 병합
            $timetables = array_merge($timetables, $temp);
        }

        // 03. 요일-교시별 정렬
        usort($timetables, function($a, $b) {
            if($a->day_of_week == $b->day_of_week) {
                if($a->period == $b->period) return 0;

                return $a->period > $b->period ? 1 : -1;
            };

            return $a->day_of_week > $b->day_of_week ? 1 : -1;
        });

        return response()->json(new ResponseObject(
            true, $timetables
        ), 200);
    }
}
