<?php

use Illuminate\Database\Seeder;

use App\Term;
use Illuminate\Support\Carbon;

class TermsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $terms = [
            '2017-1st_term'     => [
                'start'     => Carbon::createFromDate(2017, 3, 2),
                'end'       => Carbon::createFromDate(2017, 6, 23)
            ],

            '2017-summer_vacation'     => [
                'start'     => Carbon::createFromDate(2017, 6, 24),
                'end'       => Carbon::createFromDate(2017, 8, 27)
            ],

            '2017-2nd_term'     => [
                'start'     => Carbon::createFromDate(2017, 8, 28),
                'end'       => Carbon::createFromDate(2017, 12, 15)
            ],

            '2017-winter_vacation'     => [
                'start'     => Carbon::createFromDate(2017, 12, 16),
                'end'       => Carbon::createFromDate(2018, 3, 1)
            ],
            '2018-1st_term'     => [
                'start'     => Carbon::createFromDate(2018, 3, 2),
                'end'       => Carbon::createFromDate(2018, 7, 1)
            ],

            '2018-summer_vacation'     => [
                'start'     => Carbon::createFromDate(2018, 7, 2),
                'end'       => Carbon::createFromDate(2018, 8, 26)
            ],

            '2018-2nd_term'     => [
                'start'     => Carbon::createFromDate(2018, 8, 27),
                'end'       => Carbon::createFromDate(2018, 12, 17)
            ],

            '2018-winter_vacation'     => [
                'start'     => Carbon::createFromDate(2018, 12, 18),
                'end'       => Carbon::createFromDate(2019, 3, 1)
            ],
        ];

        foreach($terms as $key => $term) {
            $period = explode('-', $key);

            $model = new Term();
            $model->fill([
                'year'      => $period[0],
                'term'      => $period[1],
                'start'     => $term['start']->format('Y-m-d'),
                'end'       => $term['end']->format("Y-m-d")
            ]);

            if($model->save()) {
                echo "{$model->term} of {$model->year} is generated!!\n";
            }
        }
    }
}
