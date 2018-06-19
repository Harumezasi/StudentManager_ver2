<?php

namespace App\Http\Controllers;

use App\Exceptions\NotValidatedException;
use Illuminate\Http\Request;
use App\Schedule;
use Illuminate\Support\Carbon;
use Validator;

/**
 *  클래스명:               AdminController
 *  설명:                   관리자에게 제공하는 관련 기능들을 정의하는 클래스
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 *
 *  함수 목록
 *      - 메인
 *          = index():                          관리자의 메인 페이지를 출력
 */
class AdminController extends Controller
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

    // 회원 관리


    // 일정 관리
    // 공통일정 조회
    public function selectCommonSchedule(Request $request) {
        // 01. 요청 메시지 유효성 검사
        $validator = Validator::make($request->all(), [
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $startDate  = Carbon::parse($request->post('start_date'));
        $endDate    = Carbon::parse($request->post('end_date'));
        $schedules  = Schedule::selectBetweenDate($startDate->format("Y-m-d"), $endDate->format('Y-m-d'))
                        ->whereNull('class_id')->orderBy('start_date')->get()->all();

        return response()->json(new ResponseObject(
            true, $schedules
        ), 200);
    }

    // 공통일정 추가

    /**
     *  함수명:                         insertCommonSchedule
     *  함수 설명:                      모든 지도반의 공통 일정을 등록
     *  만든날:                         2018년 6월 11일
     *
     *  매개변수 목록
     *  @param Request $request :        요청 메시지
     *
     *  지역변수 목록
     *
     *  반환값
     *  @return                          \Illuminate\Http\JsonResponse
     *
     *  예외
     *  @throws                          NotValidatedException
     */
    public function insertCommonSchedule(Request $request) {
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
            'type'              => Schedule::TYPE['common'],
            'class_id'          => NULL,
            'holiday_flag'      => $holidayFlag,
            'include_flag'      => $includeFlag,
            'sign_in_time'      => is_null($signInTime) ? $signInTime : $signInTime->format('H:i:s'),
            'sign_out_time'     => is_null($signOutTime) ? $signOutTime : $signOutTime->format('H:i:s'),
            'contents'          => $contents
        ];

        if(Schedule::insert($setData)) {
            // 일정 등록 성공 => 성공 메시지 반환
            return response()->json(new ResponseObject(
                true, __('response_message.insert_success', ['element' => __('ada.schedule_common')])
            ), 200);
        } else {
            return response()->json(new ResponseObject(
                false, __('response_message.insert_failed', ['element' => __('ada.schedule_common')])
            ), 200);
        }
    }

    // 공통일정 수정
    public function updateCommonSchedule(Request $request) {
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
        if(!$schedule->typeCheck(Schedule::TYPE['common'])) {
            // 일정 유형이 공통이 아닌 경우 => 데이터에 대한 접근 거부
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
                true, __('response_message.update_success', ['element' => __('ada.schedule_common')])
            ), 200);
        } else {
            // 갱신 실패
            return response()->json(new ResponseObject(
                false, __('response_message.update_failed', ['element' => __('ada.schedule_common')])
            ), 200);
        }
    }

    // 공통일정 삭제
    public function deleteCommonSchedule(Request $request) {
        // 01. 요청 유효성 검증
        $validator = Validator::make($request->all(), [
            'id'        => 'required|exists:schedules,id'
        ]);

        if($validator->fails()) {
            throw new NotValidatedException($validator->errors());
        }

        // 02. 데이터 획득
        $schedule = Schedule::findOrFail($request->post('id'));
        if(!$schedule->typeCheck(Schedule::TYPE['common'])) {
            throw new NotValidatedException('해당 데이터에 대한 접근권한이 없습니다.');
        }

        // 03. 일정 삭제
        if($schedule->delete()) {
            // 갱신 성공
            return response()->json(new ResponseObject(
                true, __('response_message.delete_success', ['element' => __('ada.schedule_common')])
            ), 200);
        } else {
            // 갱신 실패
            return response()->json(new ResponseObject(
                false, __('response_message.delete_failed', ['element' => __('ada.schedule_common')])
            ), 200);
        }
    }
}
