<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               NeedCareAlert
 *  설명:                   관심학생 알림 설정 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class NeedCareAlert extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'need_care_alerts';
    protected   $fillable = [
        'manager', 'days_unit', 'notification_flag', 'count', 'alert_std_flag'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         professor
     *  함수 설명:                      관심학생 알림 조건 목록 테이블의 교수 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function professor() {
        return $this->belongsTo('App\Professor', 'prof_id', 'id');
    }



    // 03. 스코프 정의


    // 04. 클래스 메서드 정의


    // 05. 멤버 메서드 정의
}
