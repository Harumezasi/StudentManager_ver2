<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Comment
 *  설명:                   코멘트 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class Comment extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'comments';
    protected   $fillable = [
        'std_id', 'prof_id', 'content', 'year', 'term'
    ];

    public      $timestamps = false;


    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         professor
     *  함수 설명:                      코멘트 테이블의 교수 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function professor() {
        return $this->belongsTo('App\Professor', 'prof_id', 'id');
    }


    /**
     *  함수명:                         student
     *  함수 설명:                      코멘트 테이블의 학생 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function student() {
        return $this->belongsTo('App\Student', 'std_id', 'id');
    }

    /**
     *  함수명:                         student
     *  함수 설명:                      코멘트 테이블의 학생 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function terms() {
        return $this->belongsTo('App\Term', ['year', 'term'], ['year', 'term']);
    }



    // 03. 스코프 정의
    /**
     *  함수명:                         scopeDate
     *  함수 설명:                      언제 등록된 코멘트인지 조회기간을 지정
     *  만든날:                         2018년 4월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     *  @param $argDate:                조회 학기 지정
     */
    public function scopeTerm($query, $argDate) {
        $date = explode('-', $argDate);

        return $query->where([
            ['year', $date[0]], ['term', $date[1]]
        ]);
    }



    // 04. 클래스 멤버 메서드 정의



    // 05. 멤버 메서드 정의
}
