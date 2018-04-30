<?php

use Illuminate\Database\Seeder;
use App\Student;
use App\Attendance;
use Illuminate\Support\Carbon;

/**
 *  클래스명:               AttendancesTableSeeder
 *  설명:                   출석 더미 데이터를 생성하는 시더
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 26일
 */
class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // 01. 변수 선언
        $students       = Student::all();
        //$students = Student::where('id', 1401213)->get()->all();

        // 02. 학생별 더미 출석 데이터 생성
        // 학생별 최근 출석일 조회
        foreach($students as $student) {
            // 해당 학생의 최근 출석일 획득
            $regDate = is_null($student->attendances->max('reg_date')) ?
                null : Carbon::createFromFormat('Y-m-d', $student->attendances->max('reg_date'))->startOfDay();

            // 반복문 시작 시점 지정
            if(is_null($regDate)) {
                // max값이 존재하지 않으면 => 2018년 3월 1일부터 시작,
                $regDate = Carbon::createFromDate(2018, 3, 1)->startOfDay();

            } else if(today()->lte($regDate)) {
                // 등록된 최대 일자가 오늘과 같은 경우 => 함수 종료
                echo "{$student->id}'s attendance records are already exists!\n";
                return;
            }
 
            // 일 단위로 순회하는 반복문
            for ($regDate->addDay(); today()->gte($regDate); $regDate->addDay()) {
                // 주말인 경우 -> 50% 확률로 출석
                if($regDate->isWeekend()) {
                    if(rand(0, 1) == 0) {
                        continue;
                    }
                }

                // 1% 확률로 결석
                if(rand(0, 99) == 0) {
                    Attendance::insert([
                        'std_id'            => $student->id,
                        'reg_date'          => $regDate->format('Y-m-d'),
                        'sign_in_time'      => null,
                        'sign_out_time'     => null,
                        'lateness_flag'     => 'good',
                        'early_leave_flag'  => 'good',
                        'absence_flag'      => 'unreason',
                        'detail'            => ''
                    ]);

                    continue;
                }

                // 등/하교 시간 획득
                $signInLimit = $student->studyClass->sign_in_time;
                $signOutLimit = $student->studyClass->sign_out_time;

                // 출석 데이터 삽입
                Attendance::insert([
                    'std_id'            => $student->id,
                    'reg_date'          => $regDate->format('Y-m-d'),
                    'sign_in_time'      => ($signInTime = Carbon::create($regDate->year, $regDate->month, $regDate->day,
                                            rand(6, 9), rand(0, 60), rand(0, 60)))->format('Y-m-d H:i:s'),
                    'sign_out_time'     => ($signOutTime = Carbon::create($regDate->year, $regDate->month, $regDate->day,
                                            rand(20, 23), rand(0, 60), rand(0, 60)))->format('Y-m-d H:i:s'),
                    'lateness_flag'     => $signInTime->format('H:i:s') > $signInLimit ? 'unreason' : 'good',
                    'early_leave_flag'  => $signOutTime->format('H:i:s') < $signOutLimit ? 'unreason' : 'good',
                    'absence_flag'      => 'good',
                    'detail'            => ''
                ]);

                echo "{$student->id}'s attendance data of {$regDate->format('Y-m-d')} is created!!!\n";
            }
        }
    }
}
