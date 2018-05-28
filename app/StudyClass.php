<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               StudyClass
 *  설명:                   반 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class StudyClass extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'study_classes';
    protected   $fillable = [
        'tutor', 'name', 'sign_in_time', 'sign_out_time',
        'ada_search_period', 'lateness_count', 'early_leave_count', 'absence_count', 'study_usual', 'study_recent',
        'low_reflection', 'low_score', 'recent_reflection', 'recent_score'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         professor
     *  함수 설명:                      반 테이블의 교수 테이블에 대한 1:1 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function professor() {
        return $this->belongsTo('App\Professor', 'tutor', 'id');
    }

    /**
     *  함수명:                         students
     *  함수 설명:                      반 테이블의 학생 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function students() {
        return $this->hasMany('App\Student', 'study_class', 'id');
    }

    /**
     *  함수명:                         subjects
     *  함수 설명:                      반 테이블의 강의 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 5월 8일
     */
    public function subjects() {
        return $this->hasMany('App\Subject', 'join_class', 'id');
    }



    // 03. 스코프 정의

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
    // 내 반 학생들을 조회
    public function selectMyStudents() {
        return $this->students()->join('users', 'users.id', 'students.id')
            ->select([ 'users.id', 'users.name' ]);
    }


    // 학기별 시간표 조회
    public function selectTimetables($term) {
        return $this->subjects()->term($term)
            ->join('timetables', 'timetables.subject_id', 'subjects.id')
            ->join('users', 'users.id', 'subjects.professor')
            ->orderBy('timetables.day_of_week')
            ->orderBy('timetables.period')
            ->select([
                'timetables.id', 'timetables.day_of_week', 'timetables.period', 'timetables.classroom',
                'subjects.name as subject_name', 'users.name as prof_name'
            ]);
    }

    // 학생분류 기준 수정
    public function updateCriteria(Array $data) {
        // 01. 데이터 설정
        $this->ada_search_period    = $data['ada_search_period'];
        $this->lateness_count       = $data['lateness_count'];
        $this->early_leave_count    = $data['early_leave_count'];
        $this->absence_count        = $data['absence_count'];
        $this->study_usual          = $data['study_usual'];
        $this->study_recent         = $data['study_recent'];
        $this->low_reflection       = $data['low_reflection'];
        $this->low_score            = $data['low_score'];
        $this->recent_reflection    = $data['recent_reflection'];
        $this->recent_score         = $data['recent_score'];

        // 02. 갱신
        return $this->save();
    }
}