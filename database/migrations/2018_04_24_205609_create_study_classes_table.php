<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateStudyClassesTable
 *  설명:                   학반 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateStudyClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_classes', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             *
             *  id          unsigned int            primary key
             *              데이터 구분 번호
             *
             *  tutor       varchar(30)             foreign key, not null
             *              담당 지도교수
             *
             *  name        varchar(30)             not null
             *              반 이름
             *
             *  school_time time                    not null
             *              등교 시각
             *
             *  home_time   time                    not null
             *              하교 시각
             */
            $table->increments('id');
            $table->string('tutor', 30);
            $table->string('name', 30);
            $table->time('sign_in_time')->default('09:00:00');
            $table->time('sign_out_time')->default('21:00:00');

            /**
             *  02. 제약조건 정의
             */
            //$table->primary('id');
            $table->foreign('tutor')->references('id')->on('professors')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('study_classes');
    }
}
