<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *  클래스명:               JoinList
 *  설명:                   수강학생 목록 테이블에 대한 모델 속성을 정의
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class JoinList extends Model
{
    // 01. 모델 속성 정의
    protected   $table = 'join_lists';
    protected   $fillable = [
        'subject_id', 'std_id', 'achievement'
    ];

    public      $timestamps = false;



    // 02. 테이블 관계도 정의
    /**
     *  함수명:                         subject
     *  함수 설명:                      수강학생 목록 테이블의 강의 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function subject() {
        return $this->belongsTo('App\Subject', 'subject_id', 'id');
    }

    /**
     *  함수명:                         student
     *  함수 설명:                      수강학생 목록 테이블의 학생 테이블에 대한 1:* 역관계를 정의
     *  만든날:                         2018년 4월 25일
     */
    public function student() {
        return $this->belongsTo('App\Student', 'std_id', 'id');
    }



    // 03. 스코프 정의
    public function scopePeriod($query, $period) {
        $term   = explode('-', $period);

        return $query->whereIn('subject_id',
            DB::table('subjects')
                ->where([['year', $term[0]], ['term', $term[1]]])
                ->select('id')->get()->pluck('id')->all()
        );
    }



    // 04. 클래스 메서드 정의
    /**
     *  함수명:                         scopeSubject
     *  함수 설명:                      강의 ID를 지정하기 위한 스코프
     *  만든날:                         2018년 5월 4일
     *
     *  매개변수
     *  @param $query:                  질의
     *  @param $subjectId:              강의 코드
     */
    public function scopeSubject($query, $subjectId) {
        return $query->where('subject_id', $subjectId);
    }



    // 05. 멤버 메서드 정의
    public function updateAchievement() {
        // 01. 데이터 설정
        $subject = $this->subject;
        $student = $this->student;
        $stats = $student->selectStatList($subject->id);

        // 02. 학업 성취도 계산
        $achievement = [];
        foreach($stats as $type => $stat) {
            $temp = 0;
            switch($type) {
                case 'final':
                    $temp = $stat['average'] * $subject->final_refelction;
                    break;
                case 'midterm':
                    $temp = $stat['average'] * $subject->midterm_reflection;
                    break;
                case 'homework':
                    $temp = $stat['average'] * $subject->homework_reflection;
                    break;
                case 'quiz':
                    $temp = $stat['average'] * $subject->quiz_reflection;
                    break;
            }
            //echo "{$subject->name}의 {$type} 연산 결과는 {$temp}\n";
            array_push($achievement, $temp);
        }

        // 03. 데이터베이스 갱신
        $this->achievement = number_format(array_sum($achievement) / 100, 2);
        //echo "{$subject->name}의 학업성취도는 {$this->achievement}\n\n";

        return $this->save();
    }
}