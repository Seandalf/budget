<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::insert([
            ['name' => 'Australian Dollar', 'shortcode' => 'AUD', 'symbol' => '$'],
            ['name' => 'British Pound', 'shortcode' => 'GBP', 'symbol' => '£'],
            ['name' => 'New Zealand Dollar', 'shortcode' => 'NZD', 'symbol' => '$'],
            ['name' => 'United States Dollar', 'shortcode' => 'USD', 'symbol' => '$'],
            ['name' => 'Euro', 'shortcode' => 'EUR', 'symbol' => '€'],
        ], 'shortcode');
    }
}
