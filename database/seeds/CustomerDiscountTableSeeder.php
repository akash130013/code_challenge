<?php

use Illuminate\Database\Seeder;


class CustomerDiscountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('customer_discounts')->truncate();
        $data = [
            //for user
            ["discount" => '50', "no_of_time_given" => 15, 'remaining_times' => 15],
            ["discount" => '100', "no_of_time_given" => 12, 'remaining_times' => 12],
            ["discount" => '200',"no_of_time_given" => 10, 'remaining_times' => 10],
            ["discount" => '500',"no_of_time_given" => 8, 'remaining_times' => 8],
            ["discount" => '1000',"no_of_time_given" => 5, 'remaining_times' => 5],
            ["discount" => '2000',"no_of_time_given" => 4, 'remaining_times' => 4],
            ["discount" => '5000',"no_of_time_given" => 2, 'remaining_times' => 2],
            ["discount" => '10000',"no_of_time_given" => 1, 'remaining_times' => 1],
        ];
        DB::table('customer_discounts')->insert($data);
    }
}
