<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Professor
 *  설명:                   교수 목록 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Professor extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'professors';
    protected   $keyType = 'string';
    protected   $guarded = [
        'id', 'name', 'office', 'photo'
    ];

    public      $timestamps = false;
    public      $incrementing = false;


    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         needCareAlerts
     *  함수 설명:                      교수 테이블의 관심학생 알림 조건 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function needCareAlerts() {
        return $this->hasMany('App\NeedCareAlert', 'manager', 'id');
    }

    /**
     *  함수명:                         comments
     *  함수 설명:                      교수 테이블의 코멘트 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function comments() {
        return $this->hasMany('App\Comment', 'prof_id', 'id');
    }

    /**
     *  함수명:                         subjects
     *  함수 설명:                      교수 테이블의 강의 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function subjects() {
        return $this->hasMany('App\Subject', 'professor', 'id');
    }

    /**
     *  함수명:                         studyClass
     *  함수 설명:                      교수 테이블의 반 테이블에 대한 1:1 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function studyClass() {
        return $this->hasOne('App\StudyClass', 'tutor', 'id');
    }

    /**
     *  함수명:                         user
     *  함수 설명:                      교수 테이블의 사용자 테이블에 대한 1:1 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function user() {
         return $this->belongsTo('App\User', 'id', 'id');
    }



    // 03. 스코프 정의


    // 04. 클래스 메서드 정의


    // 05. 멤버 메서드 정의
}
