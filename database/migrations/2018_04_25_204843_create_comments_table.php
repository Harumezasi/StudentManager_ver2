<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateCommentsTable
 *  설명:                   코멘트 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $table->increments('id');
            $table->string('std_id', 30);
            $table->string('prof_id', 30);
            $table->text('content');
            $table->year('year');
            $table->enum('term', ['1st_term', '2nd_term', 'summer_vacation', 'winter_vacation']);

            /**
             *  02. 제약조건 설정
             */
            //$table->primary('id');
            $table->foreign('std_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('prof_id')->references('id')->on('professors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
