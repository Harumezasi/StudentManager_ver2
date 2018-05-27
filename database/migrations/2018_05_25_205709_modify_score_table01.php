<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyScoreTable01
 *  설명:                   성적 테이블을 수정하는 마이그레이션 (반 평균 칼럼 추가)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 25일
 */
class ModifyScoreTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::table('scores', function(Blueprint $table) {
             $table->unsignedSmallInteger('average_score')->default(0);
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
        Schema::table('scores', function(Blueprint $table) {
            $table->dropColumn('average_score');
        });
    }
}
