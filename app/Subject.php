<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Subject
 *  설명:                   강의 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Subject extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'subjects';
    protected   $fillable = [
        'year', 'term', 'professor', 'name', 'final_reflection',
        'midterm_reflection', 'homework_reflection', 'quiz_reflection',
        'type'
    ];

    public      $timestamps = false;

    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         timetables
     *  함수 설명:                      강의 테이블의 시간표 테이블에 대한 1:* 소유관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function timetables() {
        return $this->hasMany('App\Timetable', 'subject_id', 'id');
    }

    /**
     *  함수명:                         joinList
     *  함수 설명:                      강의 테이블의 수강목록 테이블에 대한 1:* 소유관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function joinLists() {
        return $this->hasMany('App\JoinList', 'subject_id', 'id');
    }

    /**
     *  함수명:                         scores
     *  함수 설명:                      강의 테이블의 성적목록 테이블에 대한 1:* 소유관계를 정의
     *  만든날:                         2018년 5월 1일
     */
    public function scores() {
        return $this->hasMany('App\Score', 'subject_id', 'id');
    }

    /**
     *  함수명:                         professor
     *  함수 설명:                      강의 테이블의 교수 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 5월 1일
     */
    public function professor() {
        return $this->belongsTo('App\Professor', 'professor',' id');
    }

    /**
     *  함수명:                         studyClass
     *  함수 설명:                      강의 테이블의 반 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 5월 8일
     */
    public function studyClass() {
        return $this->belongsTo('App\StudyClass', 'join_class', id);
    }

    // 03. 스코프 정의
    /**
     *  함수명:                         scopeWhen
     *  함수 설명:                      조회 학기를 지정
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     *  @param $when:                   조회 기간 (연도-학기)
     */
    public function scopeTerm($query, $when) {
        $period = explode('-', $when);

        return $query->where([['year', $period[0]], ['term', $period[1]]]);
    }

    /**
     *  함수명:                         scopeJapanese
     *  함수 설명:                      강의 유형을 일본어로 설정
     *  만든날:                         2018년 5월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     */
    public function scopeJapanese($query) {
        return $query->where('type', 'japanese');
    }

    /**
     *  함수명:                         scopeMajor
     *  함수 설명:                      강의 유형을 전공으로 설정
     *  만든날:                         2018년 5월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     */
    public function scopeMajor($query) {
        return $query->where('type', 'major');
    }

    // 04. 클래스 메서드 정의



    // 05. 멤버 메서드 정의
    /**
     *  함수명:                         selectGainedScoreList
     *  함수 설명:                      취득성적 목록을 조회
     *  만든날:                         2018년 5월 3일
     *
     *  매개변수 목록
     *  @param $scoreId :               성적 유형 ID
     *
     *  반환값
     *  @return \Illuminate\Database\Query\Builder|static
     */
    public function selectGainedScoreList($scoreId) {
        return $this->joinLists()
            ->leftJoin('gained_scores', function($join) use($scoreId) {
                $join->on('join_lists.std_id', 'gained_scores.std_id')
                    ->where('gained_scores.score_type', $scoreId);
            })->join('users', 'join_lists.std_id', 'users.id')
            ->select([
                'gained_scores.id', 'join_lists.std_id', 'users.name', 'gained_scores.score'
            ]);
    }

    /**
     *  함수명:                         updateReflections
     *  함수 설명:                      각 성적별 학업성취도 반영비율 수정
     *  만든날:                         2018년 5월 4일
     *
     *  매개변수 목록
     *  @param array $reflections:      수정할 반영비 정보
     *
     *  반환값
     *  @return bool
     */
    public function updateReflections(Array $reflections) {
        $this->final_reflection     = $reflections['final'];
        $this->midterm_reflection   = $reflections['midterm'];
        $this->homework_reflection  = $reflections['homework'];
        $this->quiz_reflection      = $reflections['quiz'];

        if($this->save()) {
            return true;
        } else {
            return false;
        }
    }

    // 수강학생 목록을 획득
    public function selectJoinedStudents() {
        $joinList = $this->joinLists()->pluck('std_id')->all();

        $data = [];
        foreach($joinList as $stdId) {
            $data[] = Student::findOrFail($stdId);
        }

        return $data;
    }
}
