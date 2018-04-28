<?php

use Illuminate\Database\Seeder;
use App\Subject;
use App\Timetable;
use Illuminate\Support\Carbon;

/**
 *  클래스명:               TimetablesTableSeeder
 *  설명:                   시간표 더미 데이터를 생성하는 시더
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 28일
 */
class TimetablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 과목별 시간표 등록
        Subject::all()->each(function ($subject) {
            $periods = [];
            if(stripos($subject->name, "객체지향") !== false) {
                // 객체지향프로그래밍 시간표
                $periods = [
                    ['day_of_week' => Carbon::TUESDAY, 'period' => 3, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::TUESDAY, 'period' => 4, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::THURSDAY, 'period' => 2, 'classroom' => '본관 200호'],
                ];
            } else if(stripos($subject->name, "웹") !== false) {
                // 웹프로그래밍 시간표
                $periods = [
                    ['day_of_week' => Carbon::MONDAY, 'period' => 4, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::FRIDAY, 'period' => 2, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::FRIDAY, 'period' => 3, 'classroom' => '본관 200호'],
                ];
            } else if(stripos($subject->name, "캡스톤") !== false) {
                // 캡스톤 디자인 시간표
                $periods = [
                    ['day_of_week' => Carbon::MONDAY, 'period' => 2, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::MONDAY, 'period' => 3, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::THURSDAY, 'period' => 6, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::THURSDAY, 'period' => 7, 'classroom' => '본관 200호'],
                ];
            } else if(stripos($subject->name, "DB") !== false) {
                $periods = [
                    ['day_of_week' => Carbon::MONDAY, 'period' => 4, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::WEDNESDAY, 'period' => 6, 'classroom' => '본관 200호'],
                    ['day_of_week' => Carbon::WEDNESDAY, 'period' => 7, 'classroom' => '본관 200호'],
                ];
            } else if(stripos($subject->name, "일본어") !== false) {
                switch(substr($subject->name, -1)) {
                    case 'A':
                        $periods = [
                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '본관 200호'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '본관 200호'],
                        ];
                        break;
                    case 'C':
                        $periods = [
                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '정보관 507호'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '정보관 507호'],
                        ];
                        break;
                }
            }

            // 각 교시별 데이터 생성
            foreach($periods as $period) {
                $timetable = new Timetable();
                $timetable->fill([
                    'subject_id'    => $subject->id,
                    'day_of_week'   => $period['day_of_week'],
                    'period'        => $period['period'],
                    'classroom'     => $period['classroom']
                ])->save();

                $numberFormat = NumberFormatter::create('en-US', NumberFormatter::ORDINAL)->format($timetable->period);
                $dayOfWeek = [
                    Carbon::MONDAY      => 'monday',
                    Carbon::TUESDAY     => 'tuesday',
                    Carbon::WEDNESDAY   => 'wednesday',
                    Carbon::THURSDAY    => 'thursday',
                    Carbon::FRIDAY      => 'friday',
                    Carbon::SATURDAY    => 'saturday',
                    Carbon::SUNDAY      => 'sunday',
                ];
                echo "{$dayOfWeek[$timetable->day_of_week]} {$numberFormat} of {$subject->name} is generated!!!\n";
            }
        });
    }
}

