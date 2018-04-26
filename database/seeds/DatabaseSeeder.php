<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\StudyClass;
use App\Student;
use App\Professor;
use App\User;

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

        StudyClass::truncate();
        Student::truncate();
        Professor::truncate();
        User::truncate();
        $this->call(UsersTableSeeder::class);
        $this->command->info('users table seeded');

        Model::reguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
