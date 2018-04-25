<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateStudentsTable
 *  설명:                   학생 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $table->string('id', 30);
            $table->unsignedInteger('study_class');
            $table->string('name', 60);
            $table->string('photo', 60)->default('');

            /**
             *  02. 제약조건 정의
             */
            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('study_class')->references('id')->on('study_classes')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
