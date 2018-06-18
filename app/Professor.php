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

    /**
     *  함수명:                         students
     *  함수 설명:                      교수 테이블의 반 테이블을 중계한 학생테이블과이 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function students() {
        return $this->hasManyThrough(
            'App\Student',
            'App\StudyClass',
            'tutor',
            'study_class',
            'id',
            'id'
        );
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
        $subjects = $this->subjects()->where('id', $subjectId);

        if($subjects->exists()) {
            return $subjects->first();
        } else {
            throw new NotValidatedException("해당 강의에 접근할 권한이 없습니다.");
        }
    }

    // 사용자가 해당 관심학생알림 설정의 소유자인지 확인
    public function isMyNeedCareAlert($needCareAlertId) {
        $needCareAlerts = $this->needCareAlerts()->where('id', $needCareAlertId);

        if($needCareAlerts->exists()) {
            return $needCareAlerts->first();
        } else {
            throw new NotValidatedException("해당 알림에 접근할 권한이 없습니다.");
        }
    }

    // 해당 학생이 사용자의 지도 학생 혹은 수강 학생인지 확인
    public function isMyStudent($stdId) {
        // 01. 수강학생 여부 확인
        $subjects = $this->subjects;

        foreach($subjects->all() as $subject) {
            $joinList = $subject->students()->where('students.id', $stdId);

            if($joinList->exists()) {
                return $joinList->first();
            }
        }

        // 02. 지도학생 여부 확인
        $students = $this->students()->where('students.id', $stdId);

        if($students->exists()) {
            return $students->first();
        } else {
            throw new NotValidatedException("해당 학생의 정보에 접근할 권한이 없습니다.");
        }
    }

    // 사용자가 해당 코멘트의 소유자인지 확인
    public function isMyComment($commentId) {
        $comments = $this->comments()->where('id', $commentId)->get()->all();

        if(sizeof($comments) > 0) {
            return $comments[0];
        } else {
            throw new NotValidatedException("해당 코멘트에 접근할 권한이 없습니다.");
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

    // 교수 회원가입 메서드
    public function insertMyInfo() {
        // 01. 사용자 정보 저장
        $user = new User();

        $user->id       = $this->id;
        $user->password = password_hash($this->password, PASSWORD_DEFAULT);
        unset($this->password);
        $user->email    = $this->email;
        unset($this->email);
        $user->phone    = $this->phone;
        unset($this->phone);
        $user->type     = $this->type;
        unset($this->type);
        $user->name     = $this->name;
        unset($this->name);
        $user->photo    = $this->photo;
        unset($this->photo);

        if($user->save() === false) {
            return false;
        }

        // 02. 교수 정보 저장
        if($this->save()) {
            return true;
        } else {
            return false;
        }
    }
}
