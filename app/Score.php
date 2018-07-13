<?php

namespace App;

use App\Exceptions\NotValidatedException;
use Illuminate\Database\Eloquent\Model;

/**
 *  클래스명:               Score
 *  설명:                   성적 목록 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class Score extends Model
{
    // 01. 모델 속성 설정
    protected   $table = 'scores';
    protected   $fillable = [
        'subject_id', 'execute_date', 'type', 'detail', 'perfect_score'
    ];

    public      $timestamps = false;


    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         subject
     *  함수 설명:                      성적목록 테이블의 강의 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function subject() {
        return $this->belongsTo('App\Subject', 'subject_id', 'id');
    }

    /**
     *  함수명:                         gainedScores
     *  함수 설명:                      성적목록 테이블의 취득성적3 테이블에 대한 1:* 소유 관계를 정의
     *  만든날:                         2018년 4월 26일
     */
    public function gainedScores() {
        return $this->hasMany('App\GainedScore', 'score_type', 'id');
    }



    // 03. 스코프 정의
    /**
     *  함수명:                         scopeStart
     *  함수 설명:                      실시일자의 조회 시작지점을 설정
     *  만든날:                         2018년 5월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     *  @param $start:                  조회 시작시점
     */
    public function scopeStart($query, $start) {
        return $query->where('execute_date', '>=', $start);
    }

    /**
     *  함수명:                         scopeEnd
     *  함수 설명:                      실시일자의 조회 종료지점을 설정
     *  만든날:                         2018년 5월 25일
     *
     *  매개변수 목록
     *  @param $query:                  질의
     *  @param $end:                    조회 종료시점
     */
    public function scopeEnd($query, $end) {
        return $query->where('execute_date', '<=', $end);
    }

    // 성적 유형을 과제로 한정
    public function scopeHomework($query) {
        return $query->where('type', 'homework');
    }

    // 성적 유형을 쪽지시험으로 한정
    public function scopeQuiz($query) {
        return $query->where('type', 'quiz');
    }

    // 성적 유형을 기말고사로 한정
    public function scopeFinal($query) {
        return $query->where('type', 'final');
    }

    // 성적 유형을 중간고사로 한정
    public function scopeMidterm($query) {
        return $query->where('type', 'midterm');
    }






    // 04. 클래스 메서드 정의


    // 05. 멤버 메서드 정의
    public function insertScoreList(array $argStdList) {
        // 해당 성적 데이터가 저장되었으면 => 각 학생의 성적 등록
        if($this->save()) {
            // 학생 목록 반복문 순회
            foreach ($argStdList as $stdId => $scoreValue) {
                // 각 학생의 성적 등록
                $gainedScore = new GainedScore();

                $gainedScore->score_type = $this->id;
                $gainedScore->std_id = $stdId;
                $gainedScore->score = $scoreValue;

                $gainedScore->save();

                // 학업 성취도 갱신
                //Student::find($stdId)->joinLists()->subject($this->subject_id)->get()[0]->updateAchievement();
            }
            // 석차 백분율 갱신
            $this->updateStandingOrder();

            // 수강학생 평균 점수 등록
            $this->updateAverageScore();

            return true;
        } else {
            return false;
        }
    }

    // 해당 학생이 취득한 성적 조회
    public function selectGainedScore($stdId) {
        $gainedScore = GainedScore::where([['std_id', $stdId], ['score_type', $this->id]])->get()->all();

        if(sizeof($gainedScore) <= 0) {
            throw new NotValidatedException(__('response.no_authority', ['contents' => __('interface.data')]));
        } else {
            return $gainedScore[0];
        }
    }

    // 석채백분율 갱신
    public function updateStandingOrder() {
        // 01. 데이터 획득
        $gainedScores = $this->gainedScores->all();

        // 02. 성적에 따른 정렬
        usort($gainedScores, function($a, $b) {
            if($a->score == $b->score) return 0;

            return $a->score < $b->score ? 1 : -1;
        });

        foreach($gainedScores as $key => $gainedScore) {
            $standingOrder = number_format($key / (sizeof($gainedScores) - 1), 2);

            $gainedScore->standing_order = $standingOrder;

            if($gainedScore->save() === false) {
                return false;
            }
        }

        return true;
    }

    // 수강학생 평균 점수 갱신
    public function updateAverageScore() {
        // 01. 취득점수 목록 획득
        $gainedScores = $this->gainedScores;

        // 02. 평균 점수 계산
        $this->average_score = number_format($gainedScores->average('score'), 0);

        return $this->save();
    }
}
