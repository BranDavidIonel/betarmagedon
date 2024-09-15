<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SitesSearch;

class SitesSearchSeeder extends Seeder
{
        /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //insert data 
        if (SitesSearch::count() == 0) {
            SitesSearch::insert([
                [
                    'name' => 'betano',
                    'link_home_page' => 'https://ro.betano.com',
                ],
                [
                    'name' => 'superbet',
                    'link_home_page' => 'https://superbet.ro',
                ],
                [
                    'name' => 'casa_pariurilor',
                    'link_home_page' => 'https://www.casapariurilor.ro',
                ],
            ]);
        }
    }
}
