<?php

use Illuminate\Database\Seeder;
use App\Subject;
use App\Score;
use Illuminate\Support\Carbon;

/**
 *  클래스명:               ScoresTableSeeder
 *  설명:                   성적 더미 데이터를 생성하는 시더
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 28일
 */
class ScoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //각 과목별 순회
        $subjects = Subject::all()->all();
        foreach($subjects as $subject) {
            // 과제 유형 지정
            $types = ['midterm', 'homework', 'quiz'];

            // 유형별로 생성되는 데이터의 개수를 다르게 설정
            foreach($types as $type) {
                $loopLimit = 0;
                switch($type) {
                    case 'midterm':
                        $loopLimit = 1;
                        break;
                    case 'homework':
                    case 'quiz':
                        $loopLimit = rand(2, 4);
                        break;
                }

                // 성적 데이터 생성
                for($iCount = 1; $iCount <= $loopLimit; $iCount++) {
                    // 과제 제출일자 생성 => 주말에는 생성하지 않음
                    $executeDate = null;
                    do {
                        $month = rand(3, today()->month);
                        if($month >= today()->month) {
                                $executeDate = Carbon::createFromDate(today()->year, today()->month, rand(1, today()->day));
                        } else {
                            // 주말을 제외한 일자에서 과제 실행
                                $date = Carbon::createFromDate(today()->year, $month, 1);
                                $executeDate = Carbon::createFromDate(today()->year, $month, rand(1, $date->endOfMonth()->day));
                        }
                    } while($executeDate->isWeekend());
                    $numberFormat = NumberFormatter::create('en-US', NumberFormatter::ORDINAL)->format($iCount);

                    // 성적 등록
                    $score = new Score();
                    $score->fill([
                        'subject_id'        => $subject->id,
                        'execute_date'      => $executeDate->format('Y-m-d'),
                        'type'              => $type,
                        'detail'            => "{$numberFormat} {$type} of {$subject->name}",
                        'perfect_score'     => rand(16, 40) * 5
                    ])->save();

                    echo "{$score->detail} is generated!!!\n";
                }
            }
        }
    }
}
