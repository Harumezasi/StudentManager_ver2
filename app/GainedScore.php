<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Subject;
use App\Student;
use App\Score;

/**
 *  클래스명:               GainedScore
 *  설명:                   취득점수 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class GainedScore extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'gained_scores';
    protected   $guarded = [
        'score_type', 'std_id', 'score'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         score
     *  함수 설명:                      취득점수 테이블의 성적 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function scoreType() {
        return $this->belongsTo('App\Score', 'score_type', 'id');
    }

    /**
     *  함수명:                         student
     *  함수 설명:                      취득점수 테이블의 학생 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function student() {
        return $this->belongsTo('App\Student', 'std_id', 'id');
    }



    // 03. 스코프 정의
    // 조회 데이터의 최저 취득점수를 제한
    public function scopeMinScore($query, $score) {
        return $query->where('score', '>=', $score);
    }

    // 조회 데이터의 최고 취득점수를 제한
    public function scopeMaxScore($query, $score) {
        return $query->where('score', '<=', $score);
    }


    // 04. 클래스 메서드 정의



    // 05. 멤버 메서드 정의

}
