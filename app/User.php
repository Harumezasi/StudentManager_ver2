<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               User
 *  설명:                   사용자 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class User extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'users';
    protected   $keyType = 'string';
    protected   $fillable = [
        'subject_id', 'day_of_week', 'period', 'classroom'
    ];

    public      $timestamps = false;
    public      $incrementing = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         professor
     *  함수 설명:                      사용자 테이블의 교수 테이블에 대한 1:1 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function professor() {
        return $this->hasOne('App\Professor', 'id', 'id');
    }

    /**
     *  함수명:                         student
     *  함수 설명:                      사용자 테이블의 학생 테이블에 대한 1:1 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function student() {
        return $this->hasOne('App\Student', 'id', 'id');
    }

    /**
     *  함수명:                         alerts
     *  함수 설명:                      사용자 테이블의 알람 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function alerts() {
        return $this->hasMany('App\Alert', 'receiver', 'id');
    }



    // 03. 스코프 정의

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
}
