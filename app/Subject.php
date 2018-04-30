<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Subject
 *  설명:                   강의 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Subject extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'subjects';
    protected   $fillable = [
        'year', 'term', 'professor', 'name', 'final_reflection',
        'midterm_reflection', 'homework_reflection', 'quiz_reflection'
    ];

    public      $timestamps = false;

    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         timetables
     *  함수 설명:                      강의 테이블의 시간표 테이블에 대한 1:* 소유관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function timetables() {
        return $this->hasMany('App\Timetable', 'subject_id', 'id');
    }

    /**
     *  함수명:                         joinList
     *  함수 설명:                      강의 테이블의 수강목록 테이블에 대한 1:* 소유관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function joinLists() {
        return $this->hasMany('App\JoinList', 'subject_id', 'id');
    }



    // 03. 스코프 정의
    /**
     *  함수명:                         scopeWhen
     *  함수 설명:                      조회 학기를 지정
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     *  @param $when:                   조회 기간 (연도-학기)
     */
    public function scopeTerm($query, $when) {
        $period = explode('-', $when);

        return $query->where([['year', $period[0]], ['term', $period[1]]]);
    }

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
}
