<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesSeeder extends Seeder
{
    // Lista de tari in limba romana fara diacritice
    private array $countriesAndRegion = [
        'afganistan',
        'albania',
        'algeria',
        'andorra',
        'angola',
        'anglia',
        'antigua si barbuda',
        'arabia saudita',
        'argentina',
        'armenia',
        'australia',
        'austria',
        'azerbaidjan',
        'bahamas',
        'bahrein',
        'bangladesh',
        'barbados',
        'belarus',
        'belgia',
        'belize',
        'benin',
        'bhutan',
        'bolivia',
        'bosnia si hertegovina',
        'botswana',
        'brazilia',
        'brunei',
        'bulgaria',
        'burkina faso',
        'burundi',
        'cabo verde',
        'cambodgia',
        'camerun',
        'canada',
        'cehia',
        'chile',
        'china',
        'cipru',
        'columbia',
        'comore',
        'republica democratica congo',
        'republica congo',
        'coreea de nord',
        'coreea de sud',
        'costa rica',
        'cote d\'ivoire',
        'croatia',
        'cuba',
        'danemarca',
        'djibouti',
        'dominica',
        'republica dominicana',
        'ecuador',
        'egipt',
        'el salvador',
        'emiratele arabe unite',
        'eritreea',
        'estonia',
        'eswatini',
        'etiopia',
        'fiji',
        'filipine',
        'finlanda',
        'franta',
        'gabon',
        'gambia',
        'georgia',
        'germania',
        'ghana',
        'grecia',
        'grenada',
        'guatemala',
        'guineea',
        'guineea-bissau',
        'guineea ecuatoriala',
        'guyana',
        'haiti',
        'honduras',
        'india',
        'indonezia',
        'irak',
        'iran',
        'irlanda',
        'islanda',
        'israel',
        'italia',
        'jamaica',
        'japonia',
        'iad',
        'iar',
        'kazahstan',
        'kenya',
        'kiribati',
        'kargazstan',
        'koreea de nord',
        'kosovo',
        'kuwait',
        'laos',
        'lesotho',
        'letonia',
        'liban',
        'liberia',
        'libia',
        'liechtenstein',
        'lituania',
        'luxemburg',
        'madagascar',
        'malawi',
        'malaezia',
        'malta',
        'maroc',
        'mauritania',
        'mauritius',
        'mexic',
        'micronezia',
        'moldova',
        'monaco',
        'mongolia',
        'montenegro',
        'mozambic',
        'namibia',
        'nauru',
        'nepal',
        'nicaragua',
        'niger',
        'nigeria',
        'norvegia',
        'noua zeelanda',
        'oman',
        'olanda',
        'pakistan',
        'palau',
        'panama',
        'papua noua guinee',
        'paraguay',
        'peru',
        'polonia',
        'portugalia',
        'qatar',
        'romania',
        'rusia',
        'rwanda',
        'saint kitts si nevis',
        'samoa',
        'san marino',
        'senegal',
        'serbia',
        'seychelles',
        'sierra leone',
        'singapore',
        'sirija',
        'slovacia',
        'slovenia',
        'somalia',
        'sri lanka',
        'statele unite ale americii',
        'suedia',
        'sudul sudanului',
        'sudan',
        'surinam',
        'siria',
        'tadjikistan',
        'tanzania',
        'thailanda',
        'timor-leste',
        'togo',
        'tonga',
        'trinidad si tobago',
        'tunisia',
        'turcia',
        'turkmenistan',
        'tuvalu',
        'ucraina',
        'uganda',
        'ungaria',
        'uruguay',
        'uzbekistan',
        'vanuatu',
        'vatican',
        'venezuela',
        'vietnam',
        'yemen',
        'zambia',
        'zimbabwe'
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Country::count() == 0) {
            foreach ($this->countriesAndRegion as $country) {
                Country::create([
                    'name' => $country,
                ]);
            }
        }
    }
}
