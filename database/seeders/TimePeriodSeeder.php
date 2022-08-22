<?php

namespace Database\Seeders;

use App\Models\TimePeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimePeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TimePeriod::insert([
            ['name' => 'daily'],
            ['name' => 'weekly'],
            ['name' => 'monthly'],
            ['name' => 'every other month'],
            ['name' => 'quarterly'],
            ['name' => 'annually'],
            ['name' => 'day'],
            ['name' => 'week'],
            ['name' => 'month'],
            ['name' => 'year'],
        ]);
    }
}
