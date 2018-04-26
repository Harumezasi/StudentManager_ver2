<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               JoinList
 *  설명:                   수강학생 목록 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class JoinList extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'join_lists';
    protected   $fillable = [
        'subject_id', 'std_id', 'achievement'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         subject
     *  함수 설명:                      수강학생 목록 테이블의 강의 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function subject() {
        return $this->belongsTo('App\Subject', 'subject_id', 'id');
    }

    /**
     *  함수명:                         student
     *  함수 설명:                      수강학생 목록 테이블의 학생 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function student() {
        return $this->belongsTo('App\Student', 'std_id', 'id');
    }



    // 03. 스코프 정의



    // 04. 클래스 메서드 정의



    // 05. 멤버 메서드 정의
}