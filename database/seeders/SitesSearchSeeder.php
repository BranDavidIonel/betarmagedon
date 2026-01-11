<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SitesSearch;

class SitesSearchSeeder extends Seeder
{
    public function run(): void
    {
        // Insert data into SitesSearch table
        $sites = [
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
        ];

        foreach ($sites as $site) {
            // Check if a site with this name already exists
            // If it doesn't exist, create a new record
            SitesSearch::firstOrCreate(
                ['name' => $site['name']], // uniqueness condition
                ['link_home_page' => $site['link_home_page']]
            );
        }

    }
}
