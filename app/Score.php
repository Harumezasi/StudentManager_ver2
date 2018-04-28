<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Score
 *  설명:                   성적 목록 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Score extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'scores';
    protected   $fillable = [
        'subject_id', 'execute_date', 'type', 'detail', 'perfect_score'
    ];

    public      $timestamps = false;


    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         subject
     *  함수 설명:                      성적목록 테이블의 강의 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function subject() {
        return $this->belongsTo('App\Subject', 'subject_id', 'id');
    }

    /**
     *  함수명:                         gainedScores
     *  함수 설명:                      성적목록 테이블의 취득성적3 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function gainedScores() {
        return $this->hasMany('App\GainedScore', 'score_type', 'id');
    }



    // 03. 스코프 정의


    // 04. 클래스 메서드 정의


    // 05. 멤버 메서드 정의
}