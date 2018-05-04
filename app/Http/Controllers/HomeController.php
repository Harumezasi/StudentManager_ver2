<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use Validator;
use Illuminate\Http\Request;
use App\User;

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
            throw new NotValidatedException();
        }

        // 01. 로그인 관련 데이터 추출
        $id     = $request->post('id');
        $pw     = $request->post('password');

        // 02. 비밀번호 검증
        $user = User::find($id);

        // 조회된 사용자 정보가 없을 경우
        if(is_null($user)) {
            return response()->json(new ResponseObject(
                false, "아이디 또는 비밀번호가 틀렸습니다."
            ), 200);
        }

        // 비밀번호 검증
        if(password_verify($pw, $user->password)) {
            // 비밀번호가 일치하는 경우 => 로그인 성공
            session()->put('user', $user->selectUserInfo());

            return response()->json(new ResponseObject(
                true, "로그인 성공!"
            ), 200);

        } else {
            // 비밀번호가 틀린 경우 => 로그인 실패
            return response()->json(new ResponseObject(
                false, "아이디 또는 비밀번호가 틀렸습니다."
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

    // 사용자 정보 불러오기
    public function getUserInfo() {
        if(!session()->exists('user')) {
            return response()->json([], 200);
        }

        return response()->json(new ResponseObject(
            true, [
                'name'  => session()->get('user')->name,
                'photo' => session()-> get('user')->photo
            ]
        ));
    }



    // 테스트

    public function session() {
        return response()->json(session()->all(), 200);
    }

    public function request(Request $request) {
        return response()->json(['header' => $request->header(), 'body' => $request->all()], 200);
    }
}