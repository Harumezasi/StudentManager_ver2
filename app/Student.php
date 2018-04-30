<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *  클래스명:               Student
 *  설명:                   학생 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Student extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'students';
    protected   $keyType = 'string';
    protected   $fillable = [
        'id', 'study_class'
    ];

    public      $timestamps = false;
    public      $incrementing = false;



    // 02. 테이블 관계도 설정
    /**
     *  함수명:                         attendances
     *  함수 설명:                      학생 테이블의 출결 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function attendances() {
        return $this->hasMany('App\Attendance', 'std_id', 'id');
    }

    /**
     *  함수명:                         user
     *  함수 설명:                      학생 테이블의 사용자 테이블에 대한 1:1 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function user() {
        return $this->belongsTo('App\User', 'id', 'id');
    }

    /**
     *  함수명:                         studyClass
     *  함수 설명:                      학생 테이블의 반 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function studyClass() {
        return $this->belongsTo('App\StudyClass', 'study_class', 'id');
    }

    /**
     *  함수명:                         comments
     *  함수 설명:                      학생 테이블의 코멘트 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function comments() {
        return $this->hasMany('App\Comment', 'std_id', 'id');
    }

    /**
     *  함수명:                         joinLists
     *  함수 설명:                      학생 테이블의 수강목록 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function joinLists() {
        return $this->hasMany('App\JoinList', 'std_id', 'id');
    }

    /**
     *  함수명:                         gainedScores
     *  함수 설명:                      학생 테이블의 취득성적 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function gainedScores() {
        return $this->hasMany('App\GainedScore', 'std_id', 'id');
    }



    // 03. 스코프 정의

    // 04. 클래스 메서드 정의

    // 05. 멤버 메서드 정의
    /**
     *  함수명:                         selectSubjectsList
     *  함수 설명:                      해당 학생이 수강하고 있는 교과목의 상세정보 목록을 조회
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $when :                  조회기간을 지정 (연도-학기)
     *
     *  지역변수 목록
     *  $period(array):                 지정된 조회기간
     *
     *  반환값
     *  @return                          $this
     */
    public function selectSubjectsList($when) {
        $period = explode('-', $when);

        return $this->joinLists()
            ->join('subjects', function($join) use($period) {
                $join->on('subjects.id', 'join_lists.subject_id')
                    ->where([['subjects.year', $period[0]], ['subjects.term', $period[1]]]);
            })->join('users', function($join) {
                $join->on('users.id', 'subjects.professor');
            })->select([
                'subjects.id', 'users.name', 'users.photo',
                'final_reflection', 'midterm_reflection',
                'homework_reflection', 'quiz_reflection'
            ]);
    }

    /**
     *  함수명:                         selectScoresList
     *  함수 설명:                      해당 학생이 해당 과목에서 취득한 성적 목록을 출력
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $subjectId:              강의 코드
     *
     *  지역변수 목록
     *  $period(array):                 지정된 조회기간
     *
     *  반환값
     *  @return                          $this
     */
    public function selectScoresList($subjectId) {
        return $this->gainedScores()
            ->rightJoin('scores', function($join) use ($subjectId){
                $join->on('gained_scores.score_type', 'scores.id')->where('subject_id', $subjectId);
            })->select([
                'scores.execute_date', 'scores.type', 'scores.detail',
                'scores.perfect_score', 'gained_scores.score AS gained_score'
            ]);
    }

    /**
     *  함수명:                         selectStatsOfType
     *  함수 설명:                      해당 학생이 해당 과목에서 성적 유형별로 취득한 성적을 조회
     *  만든날:                         2018년 4월 29일
     *
     *  매개변수 목록
     *  @param $subjectId:              강의 코드
     *
     *  지역변수 목록
     *  $period(array):                 지정된 조회기간
     *
     *  반환값
     *  @return                          $this
     */
    public function selectStatsOfType($subjectId) {
        return $this->selectScoresList($subjectId)->groupBy('type')
            ->select([
                'type', DB::raw('count(score) AS count'),
                DB::raw('sum(perfect_score) AS perfect_score'),
                DB::raw('sum(score) AS gained_score'),
                DB::raw('format((sum(score) / sum(perfect_score)), 2) * 100 AS average')
            ]);
    }
}