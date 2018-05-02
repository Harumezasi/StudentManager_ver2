<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               User
 *  설명:                   사용자 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class User extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'users';
    protected   $keyType = 'string';
    protected   $fillable = [
        'password', 'name', 'email', 'phone', 'type', 'photo'
    ];

    public      $timestamps = false;
    public      $incrementing = false;

    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         professor
     *  함수 설명:                      사용자 테이블의 교수 테이블에 대한 1:1 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function professor() {
        return $this->hasOne('App\Professor', 'id', 'id');
    }

    /**
     *  함수명:                         student
     *  함수 설명:                      사용자 테이블의 학생 테이블에 대한 1:1 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function student() {
        return $this->hasOne('App\Student', 'id', 'id');
    }

    /**
     *  함수명:                         alerts
     *  함수 설명:                      사용자 테이블의 알람 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function alerts() {
        return $this->hasMany('App\Alert', 'receiver', 'id');
    }



    // 03. 스코프 정의

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
    /**
     *  함수명:                         selectUserInfo
     *  함수 설명:                      해당 사용자의 상세 정보를 조회
     *  만든날:                         2018년 4월 26일
     */
    public function selectUserInfo() {
        $id = $this->id;

        switch($this->type) {
            case 'student':
                // 학생 회원의 상세정보 조회
                return $this->join('students', function($join) use($id) {
                        $join->on('students.id', 'users.id')->where('users.id', $id);
                    })->get(['users.id', 'name', 'phone', 'type', 'study_class', 'photo'])
                    ->all()[0];
                break;

            case 'professor':
                // 교수 회원의 상세정보 조회
                return $this->join('professors', function($join) use($id) {
                        $join->on('professors.id', 'users.id')->where('users.id', $id);
                    })->leftJoin('study_classes', 'study_classes.tutor', 'professors.id')
                    ->get(['users.id', 'users.name', 'phone', 'type', 'office', 'photo',
                        'study_classes.id as study_class'])
                    ->all()[0];
                break;
            case 'admin':
                // 운영자인 경우 -> 곧바로 반환

                return $this;
                break;
        }
    }
}
