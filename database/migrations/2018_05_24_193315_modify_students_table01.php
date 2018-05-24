<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyStudentsTable01
 *  설명:                   학생 테이블을 수정하는 마이그레이션 (관심학생에게 사랑을 주기)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 24일
 */
class ModifyStudentsTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('students', function(Blueprint $table) {
            // 주목도 수준
            $table->unsignedTinyInteger('attention_level')->default(0);

            // 주목 사유
            $table->text('attention_reason')->default("");

            // 휴학 여부 확인
            $table->boolean('stop_flag')->default(false);
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
        Schema::table('students', function(Blueprint $table) {
           $table->dropColumn('attention_level');
           $table->dropColumn('attention_reason');
           $table->dropColumn('stop_flag');
        });
    }
}
