<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               ModifyUserTable01
 *  설명:                   사용자 테이블을 수정하는 마이그레이션 (비밀번호 확인용 칼럼 추가)
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 7월 16일
 */
class ModifyUserTable01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function(Blueprint $table) {
            $table->string('verify_key', 10)->nullable()->default(NULL);

            $table->unique('verify_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('verify_key');
        });
    }
}
