<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyCommentsTable01
 *  설명:                   코멘트 테이블을 수정하는 마이그레이션 (외래키 제약조건 추가)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 27일
 */
class ModifyCommentsTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('comments', function(Blueprint $table) {
            $table->foreign(['year', 'term'])->references(['year', 'term'])->on('terms')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::table('comments', function(Blueprint $table) {
            $table->dropForeign(['year', 'term']);
        });
    }
}
