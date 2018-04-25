<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateSubjectsTable
 *  설명:                   강의 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $term = ['1st_term', '2ed_term', 'summer_vacation', 'winter_vacation'];

            $table->increments('id');
            $table->year('year');
            $table->enum('term', $term);
            $table->string('professor', 30);
            $table->string('name', 60);
            $table->unsignedDecimal('final_reflection')->default(0.3);
            $table->unsignedDecimal('midterm_reflection')->default(0.3);
            $table->unsignedDecimal('homework_reflection')->default(0.2);
            $table->unsignedDecimal('quiz_reflection')->default(0.2);

            /**
             *  02. 제약조건 정의
             */
            //$table->primary('id');
            $table->foreign('professor')->references('id')->on('professors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
