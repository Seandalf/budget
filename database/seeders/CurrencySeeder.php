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
            ['name' => 'Australian Dollar', 'shortcode' => 'AUD'],
            ['name' => 'British Pound', 'shortcode' => 'GBP'],
            ['name' => 'New Zealand Dollar', 'shortcode' => 'NZD'],
            ['name' => 'United States Dollar', 'shortcode' => 'USD'],
            ['name' => 'Euro', 'shortcode' => 'EUR'],
        ], 'shortcode');
    }
}
