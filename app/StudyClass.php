<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Schedule;
use Illuminate\Support\Carbon;

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

    /**
     *  함수명:                         schedules
     *  함수 설명:                      반 테이블의 일정 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 6월 16일
     */
    public function schedules() {
        return $this->hasMany('App\Schedule', 'class_id', 'id');
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

    // 지도반의 오늘 일정을 조회
    public function selectTodaySchedule($date) {
        if(($query = $this->schedules()->date($date))->exists()) {
            // 1순위 : 지도반 일정
            return $query->first();

        } else if (($query = Schedule::date($date)->common())->exists()) {
            // 2순위 : 계열 일정
            return $query->first();

        } else if (($query = Schedule::date($date)->holiday())->exists()) {
            // 3순위 : 국가 공휴일
            return $query->first();
        }

        return false;
    }

    // 해당 일자의 휴일/평일 여부를 조회
    public function isHolidayAtThisDay($date) {
        if(($result = $this->selectTodaySchedule($date)) != false) {
            // 해당 일자에 지도반의 일정이 있을 경우
            if($result->holiday_flag) {
                return $result;
            } else {
                return false;
            }

        } else {
            // 해당 일자에 일정이 존재하지 않는 경우 => 평일 & 주말 여부로 결정
            if(Carbon::parse($date)->isWeekend()) {
                return __('ada.weekend');
            } else {
                return false;
            }
        }
    }

    // 지정된 기간 동안의 휴일 목록을 조회
    public function selectHolidaysList($start, $end) {
        // 01. 시작일 / 끝일 데이터 지정
        $startDate  = Carbon::parse($start)->startOfDay();
        $endDate    = Carbon::parse($end)->startOfDay();

        $result = [];
        for($dateCount = $startDate->copy(); $dateCount->lte($endDate); $dateCount->addDay()) {
            $temp = $dateCount->format('Y-m-d');
            if(($date = $this->isHolidayAtThisDay($temp)) != false) {
                if($date instanceof Schedule) {
                    $result[$temp] = $date->name;
                } else {
                    $result[$temp] = $date;
                }
            }
        }

        return $result;
    }
}