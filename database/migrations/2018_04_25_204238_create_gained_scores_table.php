<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateGainedScoresTable
 *  설명:                   취득점수 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class CreateGainedScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gained_scores', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $table->increments('id');
            $table->unsignedInteger('score_type');
            $table->string('std_id', 30);
            $table->unsignedSmallInteger('score');

            /**
             *  02. 제약조건 설정
             */
            //$table->primary('id');
            $table->foreign('score_type')->references('id')->on('scores')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('std_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['score_type', 'std_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gained_scores');
    }
}
