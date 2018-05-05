<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
/**
 *  클래스명:               CreateUsersTable
 *  설명:                   사용자 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 4월 24일
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            /**
             *  01. 칼럼 정의
             *
             *  id          varchar(30)             primary key
             *              사용자 ID
             *
             *  password    varchar(100)            not null
             *              사용자 비밀번호
             *
             *  email       varchar(50)             not null
             *              이메일 주소
             *
             *  phone       varchar(30)             not null
             *              사용자 연락처
             *
             *  type        enum                    {'student', 'professor', 'admin'}
             *              사용자 유형
             */
            $table->string('id', 30);
            $table->string('password', 100)->default('');
            $table->string('name', 60);
            $table->string('email', 50);
            $table->string('phone', 30);
            $table->enum('type', ['student', 'professor', 'admin']);
            $table->text('photo')->default('');

            /**
             *  02. 제약조건 정의
             */
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
