<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifySubjectsTable02
 *  설명:                   강의 테이블을 수정하는 마이그레이션 (학업성취도 관련 칼럼 삭제)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 25일
 */
class ModifySubjectsTable02 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('subjects', function(Blueprint $table) {
            $table->dropColumn('final_reflection');
            $table->dropColumn('midterm_reflection');
            $table->dropColumn('homework_reflection');
            $table->dropColumn('quiz_reflection');
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
        Schema::table('subjects', function(Blueprint $table) {
            $table->unsignedDecimal('final_reflection')->default(0.3);
            $table->unsignedDecimal('midterm_reflection')->default(0.3);
            $table->unsignedDecimal('homework_reflection')->default(0.2);
            $table->unsignedDecimal('quiz_reflection')->default(0.2);
        });
    }
}
