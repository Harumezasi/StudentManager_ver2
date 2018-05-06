<?php

namespace App;

use App\Exceptions\NotValidatedException;
use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Professor
 *  설명:                   교수 목록 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Professor extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'professors';
    protected   $keyType = 'string';
    protected   $guarded = [
        'id', 'name'
    ];

    public      $timestamps = false;
    public      $incrementing = false;


    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         needCareAlerts
     *  함수 설명:                      교수 테이블의 관심학생 알림 조건 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function needCareAlerts() {
        return $this->hasMany('App\NeedCareAlert', 'manager', 'id');
    }

    /**
     *  함수명:                         comments
     *  함수 설명:                      교수 테이블의 코멘트 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function comments() {
        return $this->hasMany('App\Comment', 'prof_id', 'id');
    }

    /**
     *  함수명:                         subjects
     *  함수 설명:                      교수 테이블의 강의 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function subjects() {
        return $this->hasMany('App\Subject', 'professor', 'id');
    }

    /**
     *  함수명:                         studyClass
     *  함수 설명:                      교수 테이블의 반 테이블에 대한 1:1 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function studyClass() {
        return $this->hasOne('App\StudyClass', 'tutor', 'id');
    }

    /**
     *  함수명:                         user
     *  함수 설명:                      교수 테이블의 사용자 테이블에 대한 1:1 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function user() {
         return $this->belongsTo('App\User', 'id', 'id');
    }



    // 03. 스코프 정의


    // 04. 클래스 메서드 정의
    /**
     *  함수명:                         allInfoList
     *  함수 설명:                      교수의 모든 정보를 조회
     *  만든날:                         2018년 4월 30일
     */
    public static function allInfoList() {
        return self::join('users', 'users.id', 'professors.id');
    }



    // 05. 멤버 메서드 정의
    /**
     *  함수명:                         isMySubject
     *  함수 설명:                      사용자가 해당 과목의 담당교수인지 조회
     *  만든날:                         2018년 4월 30일
     */
    public function isMySubject($subjectId) {
        $subjects = $this->subjects()->where('id', $subjectId)->get()->all();

        if(sizeof($subjects) > 0) {
            return $subjects[0];
        } else {
            throw new NotValidatedException("해당 강의에 접근할 권한이 없습니다.");
        }
    }

    // 사용자가 해당 관심학생알림 설정의 소유자인지 확인
    public function isMyNeedCareAlert($needCareAlertId) {
        $needCareAlerts = $this->needCareAlerts()->where('id', $needCareAlertId)->get()->all();

        if(sizeof($needCareAlerts) > 0) {
            return $needCareAlerts[0];
        } else {
            throw new NotValidatedException("해당 알림에 접근할 권한이 없습니다.");
        }
    }

    // 해당 학생이 사용자의 지도 학생인지 확인
    public function isMyStudent($stdId) {
        $students = $this->studyClass->students()->where('id', $stdId)->get()->all();

        if(sizeof($students) > 0) {
            return $students[0];
        } else {
            throw new NotValidatedException("해당 학생의 정보에 접근할 권한이 없습니다.");
        }
    }

    // 교수 정보 갱신 메서드
    public function updateMyInfo(Array $dataArray) {
        // 01. 부모 테이블(사용자)의 데이터 갱신
        $user = $this->user;

        if(isset($dataArray['password']))
            $user->password = password_hash($dataArray['password'], PASSWORD_DEFAULT);
        if(isset($dataArray['email']))      $user->email    = $dataArray['email'];
        if(isset($dataArray['phone']))      $user->phone    = $dataArray['phone'];
        if(isset($dataArray['photo']))      $user->photo    = $dataArray['photo'];

        if($user->save() !== true) return false;

        // 02. 교수 정보 갱신
        if(isset($dataArray['office']))     $this->office   = $dataArray['office'];

        if($this->save() !== true) return false;

        return true;
    }
}
