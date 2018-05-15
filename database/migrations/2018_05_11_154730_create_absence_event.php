<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAbsenceEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         *  어제 출석을 안 한 학생을 대상으로 결석 처리를 하는 시스템
         *
         */
        $startDay = today()->addDay()->format('Y-m-d');
        DB::unprepared("
            CREATE EVENT IF NOT EXISTS insert_absence_daily
            ON SCHEDULE
            EVERY 1 DAY
            STARTS str_to_date( date_format('{$startDay}', '%Y-%m-%d 08:30:00'), '%Y-%m-%d %H:%i:%s' )
            DO          
            INSERT INTO attendances(std_id, reg_date, sign_in_time, sign_out_time, lateness_flag, early_leave_flag, absence_flag, detail)
            select id, date_sub(curdate(), interval 1 day), null, null, 'good', 'good', 'unreason', '{\"sign_in_message\":\"\",\"sign_out_message\":\"\"}'
            from students
            where id not in (
            select students.id
            from students
            join attendances
            on students.id = attendances.std_id
            and attendances.reg_date = date_sub(curdate(), interval 1 day)
            )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::unprepared("
            DROP EVENT insert_absence_daily
        ");
    }
}
