<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Schedule
 *  설명:                   일정 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 6월 8일
 */
class Schedule extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'schedules';
    protected   $fillable = [
        'id', 'start_date', 'end_date', 'name', 'type', 'class_id', 'holiday_flag', 'sign_in_time', 'sign_out_time', 'contents'
    ];

    public      $timestamps = false;
    public      $incrementing = false;

    public const TYPE = [
        'holidays' => 'holidays', 'common' => 'common', 'class' => 'class'
    ];


    // 02. 테이블 관계도 정의
    public function studyClass() {
        return $this->belongsTo('App\StudyClass', 'class_id', 'id');
    }


    // 03. 스코프 정의



    // 04. 클래스 메서드 정의




    // 05. 멤버 메서드 정의
    public function typeCheck($type) {
        if($this->type == $type) {
            return true;
        } else {
            return false;
        }
    }
}
