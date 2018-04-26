<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Attendance
 *  설명:                   출석 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
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
     *  함수명:                         scopeWhen
     *  함수 설명:                      조회기간을 설정
     *  만든날:                         2018년 4월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     *  @param $start:                  조회 시작시점
     *  @param $end:                    조회 종료시점
     */
    public function scopeWhen($query, $start, $end) {
        return $query->where([
             ['reg_date', '>=', $start], ['reg_date', '<=', $end]
        ]);
    }



    // 04. 클래스 메서드 정의



    // 05. 멤버 메서드 정의
}