<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               StudyClass
 *  설명:                   반 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class StudyClass extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'study_classes';
    protected   $fillable = [
        'tutor', 'name', 'sign_in_time', 'sign_out_time'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         professor
     *  함수 설명:                      반 테이블의 교수 테이블에 대한 1:1 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function professor() {
        return $this->belongsTo('App\Professor', 'tutor', 'id');
    }

    /**
     *  함수명:                         students
     *  함수 설명:                      반 테이블의 학생 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function students() {
        return $this->hasMany('App\Student', 'study_class', 'id');
    }



    // 03. 스코프 정의

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
}