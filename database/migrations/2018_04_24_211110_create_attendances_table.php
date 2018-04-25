<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateAttendancesTable
 *  설명:                   출결 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             */
            // 지각 구분자, 조퇴 구분자, 결석 구분자에 사용할 Enum List
            $enum = [
                'good', 'unreason', 'personal', 'sick', 'hometown', 'accident', 'disaster', 'etc'
            ];

            $table->increments('id');
            $table->string('std_id', 30);
            $table->date('reg_date');
            $table->dateTime('sign_in_time')->nullable();
            $table->dateTime('sign_out_time')->nullable();
            $table->enum('lateness_flag', $enum)->default('good');
            $table->enum('early_leave_flag', $enum)->default('good');
            $table->enum('absence_flag', $enum)->default('good');
            $table->text('detail');

            /**
             *  02. 제약조건 정의
             */
            //$table->primary('id');
            $table->foreign('std_id')->references('id')->on('students')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['std_id', 'reg_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
