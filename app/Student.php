<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Student
 *  설명:                   학생 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Student extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'students';
    protected   $keyType = 'string';
    protected   $fillable = [
        'id', 'study_class'
    ];

    public      $timestamps = false;
    public      $incrementing = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         attendances
     *  함수 설명:                      학생 테이블의 출결 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function attendances() {
        return $this->hasMany('App\Attendance', 'std_id', 'id');
    }

    /**
     *  함수명:                         user
     *  함수 설명:                      학생 테이블의 사용자 테이블에 대한 1:1 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function user() {
        return $this->belongsTo('App\User', 'id', 'id');
    }

    /**
     *  함수명:                         studyClass
     *  함수 설명:                      학생 테이블의 반 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function studyClass() {
        return $this->belongsTo('App\StudyClass', 'study_class', 'id');
    }

    /**
     *  함수명:                         comments
     *  함수 설명:                      학생 테이블의 코멘트 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function comments() {
        return $this->hasMany('App\Comment', 'std_id', 'id');
    }

    /**
     *  함수명:                         joinLists
     *  함수 설명:                      학생 테이블의 수강목록 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function joinLists() {
        return $this->hasMany('App\JoinList', 'std_id', 'id');
    }

    /**
     *  함수명:                         gainedScores
     *  함수 설명:                      학생 테이블의 취득성적 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function gainedScores() {
        return $this->hasMany('App\GainedScore', 'std_id', 'id');
    }



    // 03. 스코프 정의

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
}
