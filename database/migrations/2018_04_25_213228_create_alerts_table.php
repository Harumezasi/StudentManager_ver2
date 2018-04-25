<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateAlertsTable
 *  설명:                   알림 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 25일
 */
class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $table->increments('id');
            $table->string('receiver', 30);
            $table->text('content');

            /**
             *  02. 제약조건 설정
             */
            //$table->primary('id');
            $table->foreign('receiver')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alerts');
    }
}
