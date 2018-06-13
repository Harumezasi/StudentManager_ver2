<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Attendance
 *  설명:                   출석 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 *
 *  메서드 목록
 *  - 테이블 관계
 *      = student()
 *          : 출결 테이블의 학생 테이블에 대한 1:* 역관계를 정의
 *
 *
 *
 *  - 스코프
 *      = scopeStart($query, $start)
 *          : 조회 시작시기를 설정
 *
 *      = scopeEnd($query, $end)
 *          : 조회 종료시기를 설정
 *
 *      = scopeSignIn($query)
 *          : 정상 출석일을 조회
 *
 *      = scopeLateness($query)
 *          : 무단 지각 데이터만을 조회
 *
 *      = scopeEarlyLeave($query)
 *          : 무단 조퇴 데이터만을 조회
 *
 *      = scopeAbsence($query)
 *          : 무단 결석 데이터만을 조회
 */
class Attendance extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'attendances';
    protected   $guarded = [
        'std_id', 'reg_date', 'sign_in_time', 'sign_out_time',
        'lateness_flag', 'early_leave_flag', 'absence_flag', 'detail'
    ];

    public      $timestamps = false;



    // 02. 테이블 관게도 정의
    /**
     *  함수명:                         student
     *  함수 설명:                      출결 테이블의 학생 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function student() {
        return $this->belongsTo('App\Student', 'std_id', 'id');
    }


    // 03. 스코프 정의

    /**
     *  함수명:                         scopeStart
     *  함수 설명:                      조회 시작시기를 설정
     *  만든날:                         2018년 4월 25일
     *
     *  매개변수 목록
     *  @param $query :                  질의
     *  @param $start :                  조회 시작시점
     *  @return
     */
    public function scopeStart($query, $start) {
        return $query->where('reg_date', '>=', $start);
    }

    /**
     *  함수명:                         scopeEnd
     *  함수 설명:                      조회 종료시기를 설정
     *  만든날:                         2018년 4월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     *  @param $end:                    조회 종료시점
     */
    public function scopeEnd($query, $end) {
        return $query->where('reg_date', '<=', $end);
    }


    /**
     *  함수명:                         scopeSignIn
     *  함수 설명:                      정상 출석일을 조회
     *  만든날:                         2018년 4월 28일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     */
    public function scopeSignIn($query) {
        return $query->where([['lateness_flag', 'good'], ['absence_flag', 'good']]);
    }

    /**
     *  함수명:                         scopeLateness
     *  함수 설명:                      무단 지각 데이터만을 조회
     *  만든날:                         2018년 4월 28일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     */
    public function scopeLateness($query) {
        return $query->where('lateness_flag', '!=', 'good');
    }

    /**
     *  함수명:                         scopeEarlyLeave
     *  함수 설명:                      무단 조퇴 데이터만을 조회
     *  만든날:                         2018년 4월 28일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     */
    public function scopeEarlyLeave($query) {
        return $query->where('early_leave_flag', '!=', 'good');
    }

    /**
     *  함수명:                         scopeAbsence
     *  함수 설명:                      무단 결석 데이터만을 조회
     *  만든날:                         2018년 4월 28일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     */
    public function scopeAbsence($query) {
        return $query->where('absence_flag', '!=', 'good');
    }

    // 등록일자 역순 정렬
    public function scopeOrderDesc($query) {
        return $query->orderBy('reg_date', 'desc');
    }

    // 최근 8주간 데이터 조회
    public function scopeRecent($query) {
        // 01. 관측 일자 지정 : 지난 주로부터 8주간
        $endDate    = today()->subWeek()->endOfWeek();
        $startDate  = $endDate->copy()->subWeeks(8)->startOfWeek();

        return $query->start($startDate->format('Y-m-d'))->end($endDate->format('Y-m-d'));
    }


    // 04. 클래스 메서드 정의



    // 05. 멤버 메서드 정의
}