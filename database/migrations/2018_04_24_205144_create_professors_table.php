<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateProfessorsTable
 *  설명:                   교수 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateProfessorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('professors', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             *
             *  id          varchar(30)             primary key, foreign key
             *              교수 ID
             *
             *  name        varchar(60)             not null
             *              이름
             *
             *  office      varchar(60)             not null
             *              연구실 위치
             *
             *  photo       varchar(60)             not null
             *              사진 경로
             */
            $table->string('id', 30);
            $table->string('office', 60);

            /**
             *  02. 제약조건 정의
             */
            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professors');
    }
}
