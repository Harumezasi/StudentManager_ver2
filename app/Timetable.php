<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Timetable
 *  설명:                   시간표 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Timetable extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'timetables';
    protected   $fillable = [
        'subject_id', 'day_of_week', 'period', 'classroom'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         subject
     *  함수 설명:                      시간표 테이블의 강의 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function subject() {
        return $this->belongsTo('App\Subject', 'subject_id', 'id');
    }



    // 03. 스코프 정의

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
}