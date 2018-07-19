<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyTimetablesTable01
 *  설명:                   시간표 테이블을 수정하는 마이그레이션 (강의-요일-교시 유니크 인덱스 추가)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 7월 19일
 */
class ModifyTimetablesTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('timetables', function(Blueprint $table) {
             $table->unique(['subject_id', 'day_of_week', 'period'], 'timetable_subject_day_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('timetables', function(Blueprint $table) {
            $table->dropUnique('timetable_subject_day_period_unique');
        });
    }
}
