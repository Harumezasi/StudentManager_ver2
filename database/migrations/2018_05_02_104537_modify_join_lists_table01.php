<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyJoinListsTable01
 *  설명:                   수강목록 테이블을 수정하는 첫번째 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class ModifyJoinListsTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('join_lists', function(Blueprint $table) {
             $table->dropColumn('achievement');
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
        Schema::table('join_lists', function(Blueprint $table) {
            $table->unsignedDecimal('achievement', 3, 2);
        });
    }
}
