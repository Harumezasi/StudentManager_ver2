<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifySchedulesTable01
 *  설명:                   일정 테이블을 수정하는 마이그레이션 (주말 & 공휴일 포함여부 boolean 추가)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 6월 18일
 */
class ModifySchedulesTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("schedules", function(Blueprint $table) {
            // 01. 칼럼 추가
            $table->boolean('include_flag')->default(true);
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
        Schema::table("schedules", function(Blueprint $table) {
            // 01. 칼럼 삭제
            $table->dropColumn('include_flag');
        });
    }
}
