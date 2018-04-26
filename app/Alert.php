<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Alert
 *  설명:                   알림 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class Alert extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'alerts';
    protected   $fillable = [
        'receiver', 'content'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         user
     *  함수 설명:                      알림 테이블의 사용자 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function user() {
        return $this->belongsTo('App\User', 'receiver', 'id');
    }



    // 03. 스코프 정의
    /**
     *  함수명:                         scopeRecent
     *  함수 설명:                      최근 생성된 알람 5개를 조회
     *  만든날:                         2018년 4월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     */
    public function scopeRecent($query) {
        return $query->orderBy('id', 'desc')->limit(5);
    }



    // 04. 클래스 메서드 정의



    // 05. 멤버 메서드 정의
}