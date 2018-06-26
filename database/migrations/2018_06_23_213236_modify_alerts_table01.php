<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyAlertsTable01
 *  설명:                   알람 테이블을 수정하는 마이그레이션 (알림 구성 변경)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 6월 23일
 */
class ModifyAlertsTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('alerts', function(Blueprint $table) {
            $table->date('reg_date');
            $table->boolean('read_flag')->default(false);
            $table->string('title', 100);
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
        Schema::table('alerts', function(Blueprint $table) {
            $table->dropColumn('reg_date');
            $table->dropColumn('read_flag');
            $table->dropColumn('title');
        });
    }
}
