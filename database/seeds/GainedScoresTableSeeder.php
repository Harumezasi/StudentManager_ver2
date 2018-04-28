<?php

use Illuminate\Database\Seeder;
use App\Score;
use App\Student;
use App\GainedScore;

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
        // 성적 목록 획득
        Score::all()->each(function($score) {
            // 학생별 취득 성적 데이터 생성
            Student::all()->each(function($student) use ($score) {
                $gainedScore = new GainedScore();
                $gainedScore->fill([
                    'score_type'    => $score->id,
                    'std_id'        => $student->id,
                    'score'         => rand(($score->perfect_score / 2), $score->perfect_score)
                ])->save();

                echo "Gained score of {$student->id} at {$score->detail} is generated!!!\n";
            });
        });
    }
}
