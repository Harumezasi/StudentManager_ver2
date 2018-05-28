<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyStudyClassesTable01
 *  설명:                   반 테이블을 수정하는 마이그레이션 (학생 분류 기준 설정)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 24일
 */
class ModifyStudyClassesTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('study_classes', function(Blueprint $table) {
            $table->unsignedSmallInteger('ada_search_period')->default(7);
            $table->unsignedSmallInteger('lateness_count')->default(3);
            $table->unsignedSmallInteger('early_leave_count')->default(3);
            $table->unsignedSmallInteger('absence_count')->default(1);
            $table->unsignedSmallInteger('study_usual')->default(20);
            $table->unsignedSmallInteger('study_recent')->default(5);
            $table->unsignedDecimal('low_reflection', 3, 2)->default(0.15);
            $table->unsignedTinyInteger('low_score')->default(20);
            $table->unsignedDecimal('recent_reflection', 3, 2)->default(0.1);
            $table->unsignedTinyInteger('recent_score')->default(15);
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
        Schema::table('study_classes', function(Blueprint $table){
            $table->dropColumn('ada_search_period');
            $table->dropColumn('lateness_count');
            $table->dropColumn('early_leave_count');
            $table->dropColumn('absence_count');
            $table->dropColumn('study_usual');
            $table->dropColumn('study_recent');
            $table->dropColumn('low_reflection');
            $table->dropColumn('low_score');
            $table->dropColumn('recent_reflection');
            $table->dropColumn('recent_score');
        });
    }
}

