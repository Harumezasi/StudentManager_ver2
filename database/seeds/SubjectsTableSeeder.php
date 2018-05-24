<?php

use Illuminate\Database\Seeder;
use App\Subject;
use App\Professor;
use App\Student;
use App\JoinList;

/**
 *  클래스명:               SubjectsTableSeeder
 *  설명:                   강의 더미 데이터를 생성하는 시더
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 28일
 */
class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 교수 정보 불러오기
        $professors = Professor::all();

        // 교수별 생성과목 지정
        foreach($professors as $professor) {
            // 교과목 목록
            $subjects = [];

            switch($professor->id) {
                case 'ycjung':
                    // 정영철 교수님  => 객체지향프로그래밍, 웹프로그래밍, 캡스톤디자인
                    $subjects = [
                        ['name' => '객체지향프로그래밍(Ⅲ)', 'type' => 'major'],
                        ['name' => '웹프로그래밍(Ⅱ)', 'type' => 'major'],
                        ['name' => '캡스톤디자인(Ⅰ)', 'type' => 'major']
                    ];
                    break;
                case 'kjkim':
                    // 김기종 교수님  => DB설계
                    $subjects = [['name' => 'DB설계', 'type' => 'major']];
                    break;
                case 'seohk17':
                    // 서희경 교수님  => 실무일본어회화 - A
                    $subjects = [['name' => '실무일본어회화(Ⅱ) - A', 'type' => 'japanese']];
                    break;
                case 'figures':
                    // 기쿠치 교수님  => 실무일본어회화 - C
                    $subjects = [['name' => '실무일본어회화(Ⅱ) - C', 'type' => 'japanese']];
                    break;
                default:
                    // 김종율 교수님, 박성철 교수님
                    continue;
            }

            // 소속 반 지정
            $studyClass = Professor::find('ycjung')->studyClass;

            // 교과목 정보 생성
            foreach($subjects as $value) {
                // 강의 생성
                $subject = new Subject();
                $subject->fill([
                    'year'          => 2018,
                    'term'          => '1st_term',
                    'type'          => $value['type'],
                    'join_class'    => $studyClass->id,
                    'professor'     => $professor->id,
                    'name'          => $value['name'],
                ])->save();
                echo "Subject {$subject->name} is created!!!\n";

                // 수강 목록 생성
                foreach(Student::all()->all() as $student) {
                    $joinList = new JoinList();
                    $joinList->fill([
                        'subject_id'    => $subject->id,
                        'std_id'        => $student->id,
                    ])->save();
                    echo "{$student->id} is joined at {$subject->name}.\n";
                }
            }
        }
    }
}
