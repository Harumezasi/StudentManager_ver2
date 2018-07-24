<?php

namespace App\Console\Commands;

use App\Attendance;
use App\StudyClass;
use Illuminate\Console\Command;

/**
 *  클래스명:               CheckAbsence
 *  설명:                   매일 결석자를 조회하여 추가하는 메서드
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 6월 19일
 */

/**
 *  クラス名:               CheckAbsence
 *  説明:                   毎日欠席者を照会してデータベースに登録するタスクを定義するクラス
 *  作った人：              3-WDJ 春目指し 1401213 イ・スンミン
 *  作った日：              2018年6月19日
 */
class CheckAbsence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:check_absence';

    /**
     * The console command description.
     *
     * @var string
     */
    //　昨日欠席した学生を対象にして、欠席のデータをデータベースに登録する
    protected $description = '어제 결석한 학생을 대상으로 결석 데이터를 데이터베이스에 등록';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // 01. 각 지도반 데이터를 순회
        // 01. 各クラスのデータを得る
        $studyClasses = StudyClass::all();

        // 조회할 일자를 지정
        // 照会する日を指定
        $date = today()->subDay()->format('Y-m-d');

        foreach($studyClasses as $studyClass) {
            //　クラスのリストを順番に照会するループ

            // 오늘이 해당 지도반의 휴일인 경우 => 결석확인 X
            // 照会日がそのクラスの休日の場合 => 欠席者確認をしない
            if($studyClass->isHolidayAtThisDay($date) != false) {
                continue;
            }

            // 01. 결석 인원 목록 획득
            // 01. 欠席した学生たちのリストを照会
            $students = $studyClass->students()->leftJoin('attendances', function($join) use($date) {
                        $join->on('attendances.std_id', 'students.id')
                            ->whereDate('attendances.reg_date', $date);
                    })->whereNull('attendances.id')->pluck('students.id')->all();

            // 결석 데이터 추가
            // 欠席のデータを登録
            foreach($students as $stdId) {
                Attendance::insert([
                    'std_id'            => $stdId,
                    'reg_date'          => $date,
                    'lateness_flag'     => 'good',
                    'early_leave_flag'  => 'good',
                    'absence_flag'      => 'unreason',
                    'detail'            => json_encode(new class(){
                        public $absence_message;
                        public function __construct($detail = 'absence_message') {
                            $this->absence_message = $detail;
                        }
                    })
                ]);
            }
        }
    }
}
