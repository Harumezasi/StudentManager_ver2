<?php

use Illuminate\Database\Seeder;
use App\Score;
use App\Student;
use App\GainedScore;
use App\JoinList;
use App\Subject;
use Illuminate\Support\Carbon;

/**
 *  클래스명:               GainedScoresTableSeeder
 *  설명:                   성적 더미 데이터를 생성하는 시더
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 28일
 */
class GainedScoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 01. 학생별 취득 성적 데이터 생성
        // 성적 목록획득
        Score::all()->each(function($score) {
            // 학생 목록 획득
            $students = Subject::find($score->subject_id)->selectJoinedStudents();

            // 취득성적 데이터 생성
            foreach($students as $student) {
                $getScore = rand(($score->perfect_score / 8) * 7, $score->perfect_score);
                if(in_array($student->id, [1301235, 1401134, 1401145])) {
                    $getScore = rand(($score->perfect_score / 3), ($score->perfect_score / 8) * 7);
                } else if(in_array($student->id, [1401185, 1401213, 1601155, 1601230])) {
                    if(Carbon::parse($score->execute_date)->gt(today()->subWeeks(6))) {
                        $getScore = rand(($score->perfect_score / 3), ($score->perfect_score / 8) * 7);
                    }
                }
                $gainedScore = new GainedScore();
                $gainedScore->fill([
                    'score_type'    => $score->id,
                    'std_id'        => $student->id,
                    'score'         => $getScore
                ])->save();

                echo "Gained score of {$student->id} at {$score->detail} is generated!!!\n";
            };

            // 석차 백분율 갱신
            $score->updateStandingOrder();

            // 수강학생 평균 점수 등록
            $score->updateAverageScore();
        });
    }
}
