<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use App\Http\Middleware\Language;
use App\Mail\TempPasswordGenerated;
use App\Student;
use App\Professor;
use Validator;
use Illuminate\Http\Request;
use App\User;
use App\StudyClass;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;


/**
 *  클래스명:               HomeController
 *  설명:                   홈 화면에서 제공하는 관련 기능들을 정의 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index():                          서비스의 메인 페이지를 출력
 *
 *          = setLanguage($locale):             언어 변경 요청을 받아, 제공하는 언어 패키지를 변경
 *
 *
 *
 *      - 회원 관리
 *          = login(Request $request):          사용자 로그인을 실행
 *          = 사용자 정보 불러오기
 *
 *
 *      - 하드웨어
 *          = 오늘자 학생 출결목록 출력
 *          = 오늘자 시간표 출력
 *          =
 *
 *      - 테스트
 *          = session():                        세션 정보를 호출
 *
 *          = request():                        요청 값을 반환
 */
class HomeController extends Controller
{
    // 01. 멤버 변수 정의

    // 02. 멤버 메서드 정의
    /**
     *  함수명:                         index
     *  함수 설명:                      서비스의 메인 페이지를 출력
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
        if(session()->exists('user')) {
            $userType = session()->get('user')->type;
            return redirect(route("{$userType}.index"));
        }

        return view('index');
    }

    /**
     * 함수명:                         setLanguage
     * 함수 설명:                      언어 변경 요청을 받아, 제공하는 언어 패키지를 변경
     * 만든날:                         2018년 4월 26일
     *
     * 매개변수 목록
     * @param $locale:                 View 단에서 변경을 요청한 언어 코드
     *
     * 지역변수 목록
     * null
     *
     * 반환값
     * @return                          \Illuminate\Http\RedirectResponse
     */
    public function setLanguage($locale) {
        // 01. 언어 설정
        if(in_array($locale, config()->get('app.locales'))) {
            session()->put('locale', $locale);
        }
        app()->setLocale($locale);

        return redirect()->back();
    }



    // 회원 관리
    /**
     *  함수명:                         login
     *  함수 설명:                      사용자 로그인을 실행
     *  만든날:                         2018년 4월 26일
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
     *  @throws NotValidatedException:  유효하지 않은 요청에 대한 예외처리
     */
    public function login(Request $request) {
        // 데이터 유효성 검증
        $validator = Validator::make($request->all(), [
            'id'            => 'required',
            'password'      => 'required'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 01. 로그인 관련 데이터 추출
        $id     = $request->post('id');
        $pw     = $request->post('password');

        // 02. 비밀번호 검증
        $user = User::find($id);

        // 조회된 사용자 정보가 없을 경우
        if(is_null($user)) {
            return response()->json(new ResponseObject(
                false, __('response.wrong_id_or_password')
            ), 200);
        }

        // 비밀번호 검증
        if(password_verify($pw, $user->password)) {
            // 비밀번호가 일치하는 경우 => 로그인 성공
            session()->put('user', $user->selectUserInfo());

            return response()->json(new ResponseObject(
                true, __('response.sign_in_success')
            ), 200);

        } else {
            // 비밀번호가 틀린 경우 => 로그인 실패
            return response()->json(new ResponseObject(
                false, __('response.wrong_id_or_password')
            ), 200);
        }
    }

    /**
     * 함수명:                         logout
     * 함수 설명:                      현재 사용자의 로그아웃을 실행
     * 만든날:                         2018년 4월 26일
     *
     * 매개변수 목록
     * null
     *
     * 지역변수 목록
     * null
     *
     * 반환값
     * @return                         \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout() {
        if(session()->has('user')) {
            session()->forget('user');
        }

        return redirect(route('home.index'));
    }

    // 회원 정보 반환
    public function getUserInfo() {
        return response()->json(new ResponseObject(
            true, [
                'name'      => session()->get('user')->name,
                'type'      => session()->get('user')->type,
                'photo'     => session()->get('user')->photo_url
            ]
        ), 200);
    }

    // 회원가입 여부 확인
    public function checkJoin(Request $request) {
        // 01. 유효성 검사
        $validator = Validator::make($request->all(), [
            'type'              => 'required|in:professor,student',
            'id'                => 'required'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 회원가입 유형에 따른 검증방법 설정
        $type = $request->get('type');
        switch($type) {
            case 'student':
                // 02. 해당 학생의 회원가입 여부 검증
                $student = Student::find($request->get('id'));

                if (is_null($student)) {
                    return response()->json(new ResponseObject(
                        false, __('response.not_exists_student')
                    ), 200);
                } else if (strlen($student->user->password) <= 0) {
                    return response()->json(new ResponseObject(
                        true, $student->user->name
                    ), 200);
                } else {
                    return response()->json(new ResponseObject(
                        false, __('response.already_joined_student')
                    ), 200);
                }
            case 'professor':
                $user = User::find($request->get('id'));

                if(is_null($user)) {
                    return response()->json(new ResponseObject(
                        true, __('response.usable_id')
                    ), 200);
                } else {
                    return response()->json(new ResponseObject(
                        false, __('response.already_joined_id')
                    ), 200);
                }
        }
    }

    // 회원가입
    public function join(Request $request) {
        // 01. 유효성 검사
        $validator = Validator::make($request->all(), [
            'type'              => 'required|in:student,professor',
            'id'                => 'required',
            'id_check'          => 'required|boolean',
            'password'          => 'required|same:password_check',
            'password_check'    => 'required|same:password',
            'name'              => 'required',
            'email'             => 'required|email',
            'phone'             => 'required',
            'photo'             => 'image',
            'office'            => 'required_if:type,professor'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 설정
        $type   = $request->post('type');
        $id     = $request->post('id');

        $user   = null;
        switch($type) {
            case 'student':
                $user = Student::find($id);
                if(is_null($user)) {
                    throw new NotValidatedException(__('response.not_exists_student'));
                }
                break;
            case 'professor':
                $user = new Professor();
                $user->id       = $request->post('id');
                $user->office   = $request->post('office');
                $user->name     = $request->post('name');
                break;
        }

        $photo = $request->hasFile('photo') ? $request->file('photo') : null;
        $photoName = '';
        if(!is_null($photo)) {
            // 새 이미지 저장 => DB 사용자 정보에 새로운 이미지 경로를 지정
            $fileName = $photo->store('/',$type == 'student' ? 'std_photo' : 'prof_photo');
            $photoName = $fileName;
        }

        // 03. 사용자 정보 획득
        $user->type     = $type;
        $user->password = $request->post('password');
        $user->email    = $request->post('email');
        $user->phone    = $request->post('phone');
        $user->photo    = $photoName;

        // 04. 데이터베이스에 데이터 등록
        switch($type) {
            case 'student':
                if($user->updateMyInfo([
                    'password'  => $user->password,
                    'email'     => $user->email,
                    'phone'     => $user->phone,
                    'photo'     => $user->photo
                ])) {
                    return response()->json(new ResponseObject(
                        true, __('response.join_success')
                    ), 200);
                } else {
                    return response()->json(new ResponseObject(
                        false, __('response.join_failed')
                    ), 200);
                }
            case 'professor':
                if($user->insertMyInfo()) {
                    return response()->json(new ResponseObject(
                        true, __('response.join_success')
                    ), 200);
                } else {
                    return response()->json(new ResponseObject(
                        false, __('response.join_failed')
                    ), 200);
                }
        }
    }

    // 아이디 찾기


    // 비밀번호 찾기



    // 하드웨어
    // 학생 인증
    public function checkStudent(Request $request) {
        // 01. 요청 메시지 유효성 검증
        $validator = Validator::make($request->all(), [
            'id'        => 'required',
            'password'  => 'required'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $student = Student::find($request->post('id'));

        // ##### DB에 등록되지 않은 ID인 경우 #####
        if(is_null($student)) {
            return response()->json(new ResponseObject(
                false, __('response.wrong_id_or_password')
            ), 200);
        }

        $userInfo = $student->user;
        if(password_verify($request->post('password'), $userInfo->password)) {
            // 로그인 성공
            return response()->json(new ResponseObject(
                true, [
                    'id'    => $userInfo->id,
                    'name'  => $userInfo->name,
                    'photo' => $userInfo->selectUserInfo()->photo_url
                ]
            ), 200);

        } else if(strlen($userInfo->password) <= 0) {
            // 패스워드가 없음 => 등록되지 않은 학생
            return response()->json(new ResponseObject(
                false, __('response.not_joined')
            ), 200);

        } else {
            // 패스워드가 틀림
            return response()->json(new ResponseObject(
                false, __('response.wrong_id_or_password')
            ), 200);
        }
    }


    // 오늘자 시간표 출력
    public function getTimetableOfToday(Request $request) {
        // 01. 요청 유효성 검증
        $validDayOfWeek = implode(',', [
            Carbon::MONDAY, Carbon::TUESDAY, Carbon::WEDNESDAY, Carbon::THURSDAY, Carbon::FRIDAY
        ]);
        $validator = Validator::make($request->all(), [
            'class_id'      => 'required|exists:study_classes,id',
            'day_of_week'   => "required|in:{$validDayOfWeek}"
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 설정
        $studyClass = StudyClass::findOrFail($request->get('class_id'));
        $term       = $this->getTermValue()['this'];
        $dayOfWeek  = $request->get('day_of_week');
        $timetables = $studyClass->selectTimetables($term);
        if(is_null($dayOfWeek)) {
            $timetables = $timetables->get()->all();
        } else {
            $timetables = $timetables->where('day_of_week', $dayOfWeek)->get()->all();
        }

        return response()->json(new ResponseObject(
            true, $timetables
        ), 200);
    }

    // 오늘자 출석 현황 출력
    public function getAttendanceRecordsOfToday(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'class_id'  => 'required|exists:study_classes,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $studyClass     = StudyClass::findOrFail($request->get('class_id'));
        $student        = $studyClass->students();

        // 오늘자 출석기록 획득
        $today          = Carbon::create()->hour > 6 ?
            today()->format('Y-m-d') : today()->subDay()->format('Y-m-d');
        $attendances    = $student->join('users', 'users.id', 'students.id')
            ->leftJoin('attendances', function($join) use ($today) {
                $join->on('students.id', 'attendances.std_id')
                    ->where('attendances.reg_date', "{$today}");
            })->select('students.id', 'users.name', 'attendances.sign_in_time', 'attendances.lateness_flag')
            ->get()->all();

        // 03. View 단에 전송할 데이터 설정
        $data = [
            'reg_date'  => $today,
            'absence'   => [],
            'lateness'  => [],
            'sign_in'   => []
        ];
        foreach($attendances as $attendance) {
            if(is_null($attendance->sign_in_time)) {
                $data['absence'][] = $attendance;
                continue;
            } else {
                if($attendance->lateness_flag == 'good') {
                    $data['sign_in'][] = $attendance;
                    continue;
                } else {
                    $data['lateness'][] = $attendance;
                    continue;
                }
            }
        }

        return response()->json(new ResponseObject(
            true, $data
        ), 200);
    }


    // 테스트

    public function session() {
        return response()->json(session()->all(), 200);
    }

    public function request(Request $request) {
        return response()->json(['header' => $request->header(), 'body' => $request->all()], 200);
    }

    // 시험용 메서드
    public function test() {
        $to = 'smlee95kr@naver.com';

        return Mail::to($to)->send(new TempPasswordGenerated());
    }
}