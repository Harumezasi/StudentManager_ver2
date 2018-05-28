<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  클래스명:               CreateTermsTable
 *  설명:                   학기 테이블을 생성하는 마이그레이션
 *  만든이:                 3-WDJ 春目指し 1401213 이승민
 *  만든날:                 2018년 5월 27일
 */
class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            // 01. 칼럼 정의
            $term = ['1st_term', '2nd_term', 'summer_vacation', 'winter_vacation'];

            $table->year('year');
            $table->enum('term', $term);
            $table->date('start');
            $table->date('end');

            // 02. 제약조건 설정
            $table->primary(['year', 'term']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
}
