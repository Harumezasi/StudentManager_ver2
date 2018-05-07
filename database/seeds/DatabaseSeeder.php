<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\StudyClass;
use App\Student;
use App\Professor;
use App\User;
use App\Attendance;
use App\Subject;
use App\JoinList;
use App\Score;
use App\GainedScore;
use App\Timetable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
/*
        // 사용자 테이블 시딩
        StudyClass::truncate();
        Student::truncate();
        Professor::truncate();
        User::truncate();
        $this->call(UsersTableSeeder::class);
        $this->command->info('users table is seeded.');
*/
        // 출석 데이터 시딩
        Attendance::truncate();
        $this->call(AttendancesTableSeeder::class);
        $this->command->info('attendances table is seeded.');
/*
        // 과목 데이터 시딩
        Subject::truncate();
        JoinList::truncate();
        $this->call(SubjectsTableSeeder::class);
        $this->command->info('subjects table is seeded.');

        // 성적 데이터 시딩
        Score::truncate();
        $this->call(ScoresTableSeeder::class);
        $this->command->info('scores table is seeded.');

        // 취득성적 데이터 시딩
        GainedScore::truncate();
        $this->call(GainedScoresTableSeeder::class);
        $this->command->info('gained_scores table is seeded.');

        // 시간표 데이터 시딩
        Timetable::truncate();
        $this->call(TimetablesTableSeeder::class);
        $this->command->info('timetables table is seeded.');
*/
        Model::reguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
