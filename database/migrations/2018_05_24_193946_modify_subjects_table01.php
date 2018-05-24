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
class ModifySubjectsTable01 extends Migration
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
            $table->enum('type', ['japanese', 'major']);
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
            $table->dropColumn('type');
        });
    }
}
