<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateScoresTable
 *  설명:                   성적 데이터 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $table->unsignedInteger('subject_id');
            $table->increments('id');
            $table->date('execute_date');
            $table->enum('type', ['final', 'midterm', 'homework', 'quiz']);
            $table->text('content');
            $table->unsignedSmallInteger('perfect_score');

            /**
             *  02. 제약조건 정의
             */
            //$table->primary('id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scores');
    }
}
