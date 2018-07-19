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
            if(stripos($subject->name, "オブジェクト指向") !== false) {
                // 객체지향프로그래밍 시간표
                $periods = [
//                    ['day_of_week' => Carbon::TUESDAY, 'period' => 3, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::TUESDAY, 'period' => 4, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::THURSDAY, 'period' => 2, 'classroom' => '본관 200호'],

                    ['day_of_week' => Carbon::TUESDAY, 'period' => 3, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::TUESDAY, 'period' => 4, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::THURSDAY, 'period' => 2, 'classroom' => '本館200号'],
                ];
            } else if(stripos($subject->name, "ウェブ") !== false) {
                // 웹프로그래밍 시간표
                $periods = [
//                    ['day_of_week' => Carbon::MONDAY, 'period' => 4, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::FRIDAY, 'period' => 2, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::FRIDAY, 'period' => 3, 'classroom' => '본관 200호'],

                    ['day_of_week' => Carbon::MONDAY, 'period' => 4, 'classroom' => '本願200号'],
                    ['day_of_week' => Carbon::FRIDAY, 'period' => 2, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::FRIDAY, 'period' => 3, 'classroom' => '本館200号'],
                ];
            } else if(stripos($subject->name, "キャップストーン") !== false) {
                // 캡스톤 디자인 시간표
                $periods = [
//                    ['day_of_week' => Carbon::MONDAY, 'period' => 2, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::MONDAY, 'period' => 3, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::THURSDAY, 'period' => 6, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::THURSDAY, 'period' => 7, 'classroom' => '본관 200호'],

                    ['day_of_week' => Carbon::MONDAY, 'period' => 2, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::MONDAY, 'period' => 3, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::THURSDAY, 'period' => 6, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::THURSDAY, 'period' => 7, 'classroom' => '本館200号'],
                ];
            } else if(stripos($subject->name, "DB") !== false) {
                // DB설계 시간표
                $periods = [
//                    ['day_of_week' => Carbon::MONDAY, 'period' => 6, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::WEDNESDAY, 'period' => 6, 'classroom' => '본관 200호'],
//                    ['day_of_week' => Carbon::WEDNESDAY, 'period' => 7, 'classroom' => '본관 200호'],

                    ['day_of_week' => Carbon::MONDAY, 'period' => 6, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::WEDNESDAY, 'period' => 6, 'classroom' => '本館200号'],
                    ['day_of_week' => Carbon::WEDNESDAY, 'period' => 7, 'classroom' => '本館200号'],
                ];
            } else if(stripos($subject->name, "日本語") !== false) {
                switch(substr($subject->name, -1)) {
                    case 'A':
                        $periods = [
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '본관 200호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '본관 200호'],

                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '本館200号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '本館200号'],
                        ];
                        break;

                    case 'B':
                        $periods = [
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '정보관 102호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '정보관 102호'],

                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '情報館102号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '情報館102号'],
                        ];
                        break;

                    case 'C':
                        $periods = [
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '정보관 507호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '정보관 507호'],

                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '情報館507号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '情報館507号'],
                        ];
                        break;

                    case 'D':
                        $periods = [
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '정보관 513호'],
//                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '정보관 513호'],

                            ['day_of_week' => Carbon::MONDAY, 'period' => 8, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::MONDAY, 'period' => 9, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 8, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::TUESDAY, 'period' => 9, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 8, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::WEDNESDAY, 'period' => 9, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 8, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::THURSDAY, 'period' => 9, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 8, 'classroom' => '情報館513号'],
                            ['day_of_week' => Carbon::FRIDAY, 'period' => 9, 'classroom' => '情報館513号'],
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

