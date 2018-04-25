<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateJoinListsTable
 *  설명:                   수강학생 목록 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateJoinListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_lists', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $table->increments('id');
            $table->unsignedInteger('subject_id');
            $table->string('std_id', 30);
            $table->unsignedDecimal('achievement')->default(0);

            /**
             *  02. 제약조건 정의
             */
            //$table->primary('id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('std_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['subject_id', 'std_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('join_lists');
    }
}
