<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateNeedCareAlertTable
 *  설명:                   관심학생 선별 기준 데이터 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateNeedCareAlertTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('need_care_alert', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            $notification_flag_enum = [
                'continuative_lateness', 'continuative_early_leave', 'continuative_absence',
                'total_lateness', 'total_early_leave', 'total_absence'
            ];

            $table->increments('id');
            $table->string('manager', 30);
            $table->unsignedSmallInteger('days_unit');
            $table->enum('notification_flag', $notification_flag_enum);
            $table->unsignedSmallInteger('count');
            $table->boolean('alert_std_flag');

            /**
             *  02. 제약조건 설정
             */
            //$table->primary('id');
            $table->foreign('manager')->references('id')->on('professors')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('need_care_alert');
    }
}
