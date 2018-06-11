<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateSchedulesTable
 *  설명:                   일정 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 6월 08일
 */
class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            // 01. 칼럼 정의
            $table->increments('id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('name', 100);
            $table->enum('type', ['holidays', 'common', 'class']);
            $table->unsignedInteger('class_id')->nullable();
            $table->boolean('holiday_flag');
            $table->time('sign_in_time')->nullable();
            $table->time('sign_out_time')->nullable();
            $table->text('contents');

            // 02. 제약조건 정의
            //$table->primary('id');
            $table->foreign('class_id')->references('id')->on('study_classes')->onUpdate('cascade')->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
