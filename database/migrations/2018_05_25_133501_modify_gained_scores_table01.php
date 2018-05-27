<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


/**
 *  클래스명:               ModifyGainedScoresTable01
 *  설명:                   취득성적 테이블을 수정하는 마이그레이션 (석차백분율 칼럼 추가)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 25일
 */
class ModifyGainedScoresTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('gained_scores', function(Blueprint $table) {
            $table->unsignedDecimal('standing_order', 3, 2)->default(0);
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
        Schema::table('gained_scores', function(Blueprint $table) {
            $table->dropColumn('standing_order');
        });
    }
}
