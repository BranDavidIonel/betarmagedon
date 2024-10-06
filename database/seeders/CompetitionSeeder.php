<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Competition;
class CompetitionSeeder extends Seeder
{
    private $competitions = [
        //region anglia data
        [
            'id' => 1,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'premier league',
            'alias' => ['premier league', 'premier league cup', 'anglia premier league','premier league clasare finala', 'premier league duel clasare', 'premier league golgheter', 'premier league cup u21']
        ],
        [
            'id' => 2,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'fa cup',
            'alias' => ['fa cup', 'cupa fa calificari', 'anglia cupa fa']
        ],
        [
            'id' => 3,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'efl cup',
            'alias' => ['efl cup', 'anglia efl trophy']
        ],
        [
            'id' => 4,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'championship',
            'alias' => ['championship' , 'anglia championship']
        ],
        [
            'id' => 5,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'league one',
            'alias' => ['league one', 'anglia 1']
        ],
        [
            'id' => 6,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'league two',
            'alias' => ['league two', 'anglia 2' , 'anglia premier league 2 (u21)']
        ],
        [
            'id' => 7,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'efl trophy',
            'alias' => ['efl trophy']
        ],
        [
            'id' => 8,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'national league',
            'alias' => ['national league', 'national league south', 'national league premier division f', 'national league division one f']
        ],
        [
            'id' => 9,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'northern league',
            'alias' => ['northern league premier division', 'northern league division one west']
        ],
        [
            'id' => 10,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'isthmian league',
            'alias' => ['isthmian league premier division']
        ],
        [
            'id' => 11,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'southern league',
            'alias' => ['southern league premier division south', 'southern league premier division central']
        ],
        [
            'id' => 12,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'super league f',
            'alias' => ['super league f']
        ],
        [
            'id' => 13,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'league three',
            'alias' => ['league three', 'anglia 3']
        ],
        [
            'id' => 14,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'league four',
            'alias' => ['league four', 'anglia 4']
        ],
        [
            'id' => 15,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'league five',
            'alias' => ['league five', 'anglia 5']
        ],
        //endregion
        //region albania nu are am pus ( 0, 1) in coada
        [
            'id' => 150,
            'country_id' => 2,
            'country_name' => 'albania',
            'name' => 'kategoria superiore',
            'alias' => ['kategoria superiore', 'albania 1']
        ],
        //endregion
        //region algeria
        [
            'id' => 13,
            'country_id' => 3,
            'country_name' => 'algeria',
            'name' => 'ligue 1',
            'alias' => ['ligue 1', 'algeria 1']
        ],
        //endregion
        //region andorra nu era pus (0) in coada
        [
            'id' => 130,
            'country_id' => 4,
            'country_name' => 'andorra',
            'name' => 'primera divisio',
            'alias' => ['primera divisio', 'andorra 1']
        ],
        //endregion
        //region angora nu era pus (1) in coada
        [
            'id' => 131,
            'country_id' => 4,
            'country_name' => 'angola',
            'name' => 'girabola',
            'alias' => ['girabola', 'angola 1']
        ],
        //endregion
        //region arabia saudita data
        [
            'id' => 14,
            'country_id' => 8,
            'country_name' => 'arabia saudita',
            'name' => 'pro league',
            'alias' => ['pro league', 'arabia saudita 1' , 'professional league']
        ],
//        [
//            'id' => 15,
//            'country_id' => 8,
//            'country_name' => 'arabia saudita',
//            'name' => 'professional league',
//            'alias' => ['professional league']
//        ],
//        [
//            'id' => 16,
//            'country_id' => 8,
//            'country_name' => 'arabia saudita',
//            'name' => 'king cup',
//            'alias' => ['king cup']
//        ],
        //endregion
        //region argentina data
        [
            'id' => 17,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'torneo betano',
            'alias' => ['torneo betano']
        ],
        [
            'id' => 18,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'copa',
            'alias' => ['copa', 'cupa' ,'argentina 1']
        ],
        [
            'id' => 19,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'primera b nacional',
            'alias' => ['primera b nacional', 'argentina-primera b']
        ],
        [
            'id' => 20,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'primera b metropolitana',
            'alias' => ['primera b metropolitana']
        ],
        [
            'id' => 21,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'ligas regionales',
            'alias' => ['ligas regionales']
        ],
        [
            'id' => 22,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'copa de la liga de reserva',
            'alias' => ['copa de la liga de reserva']
        ],
        [
            'id' => 23,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'primera b metropolitana torneo de reserva',
            'alias' => ['primera b metropolitana torneo de reserva']
        ],
        [
            'id' => 24,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'primera c torneo de reserva',
            'alias' => ['primera c torneo de reserva', 'argentina-primera c']
        ],
        [
            'id' => 25,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'primera division a f',
            'alias' => ['primera division a f']
        ],
        [
            'id' => 26,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'liga profesional',
            'alias' => ['liga profesional']
        ],
        [
            'id' => 27,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'primera nacional',
            'alias' => ['primera nacional']
        ],
        [
            'id' => 28,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'cupa proyeccion final r',
            'alias' => ['cupa proyeccion final r']
        ],
        [
            'id' => 29,
            'country_id' => 9,
            'country_name' => 'argentina',
            'name' => 'reserve league',
            'alias' => ['reserve league', '2']
        ],
        //endregion
        //region australia data
        [
            'id' => 30,
            'country_id' => 11,
            'country_name' => 'australia',
            'name' => 'a league',
            'alias' => ['a league']
        ],
        [
            'id' => 31,
            'country_id' => 11,
            'country_name' => 'australia',
            'name' => 'cupa',
            'alias' => ['cupa', 'australia 1']
        ],
        //endregion
        //region austria data
        [
            'id' => 32,
            'country_id' => 12,
            'country_name' => 'austria',
            'name' => 'bundesliga',
            'alias' => ['bundesliga']
        ],
        [
            'id' => 33,
            'country_id' => 12,
            'country_name' => 'austria',
            'name' => '2 liga',
            'alias' => ['2 liga', '2liga']
        ],
        [
            'id' => 34,
            'country_id' => 12,
            'country_name' => 'austria',
            'name' => 'regionalliga',
            'alias' => ['regionalliga']
        ],
        [
            'id' => 35,
            'country_id' => 12,
            'country_name' => 'austria',
            'name' => 'state leagues',
            'alias' => ['state leagues']
        ],
        //endregion
        //region azerbaidjan data
        [
            'id' => 36,
            'country_id' => 13,
            'country_name' => 'azerbaidjan',
            'name' => 'i divizion',
            'alias' => ['i divizion']
        ],
        //endregion
        //region belgia data
        [
            'id' => 37,
            'country_id' => 19,
            'country_name' => 'belgia',
            'name' => '1a pro league',
            'alias' => ['1a pro league', 'pro league']
        ],
        [
            'id' => 38,
            'country_id' => 19,
            'country_name' => 'belgia',
            'name' => 'challenger pro league',
            'alias' => ['challenger pro league']
        ],
        //endregion
        //region bolivia data
        [
            'id' => 39,
            'country_id' => 23,
            'country_name' => 'bolivia',
            'name' => 'division profesional clausura',
            'alias' => ['division profesional clausura']
        ],
        [
            'id' => 40,
            'country_id' => 23,
            'country_name' => 'bolivia',
            'name' => 'division profesional',
            'alias' => ['division profesional']
        ],
        //endregion
        //region brazilia data
        [
            'id' => 43,
            'country_id' => 26,
            'country_name' => 'brazilia',
            'name' => 'u23',
            'alias' => ['u23']
        ],
        [
            'id' => 44,
            'country_id' => 26,
            'country_name' => 'brazilia',
            'name' => 'brasileiro serie a',
            'alias' => ['brasileiro serie a', 'brasileiro a']
        ],
        [
            'id' => 45,
            'country_id' => 26,
            'country_name' => 'brazilia',
            'name' => 'cupa',
            'alias' => ['cupa']
        ],
        [
            'id' => 46,
            'country_id' => 26,
            'country_name' => 'brazilia',
            'name' => 'brasileiro serie b',
            'alias' => ['brasileiro serie b']
        ],
        [
            'id' => 47,
            'country_id' => 26,
            'country_name' => 'brazilia',
            'name' => 'brasileiro serie c grb',
            'alias' => ['brasileiro serie c grb']
        ],
        [
            'id' => 48,
            'country_id' => 26,
            'country_name' => 'brazilia',
            'name' => 'brasileiro serie c grc',
            'alias' => ['brasileiro serie c grc']
        ],
        [
            'id' => 49,
            'country_id' => 26,
            'country_name' => 'brazilia',
            'name' => 'cupa fgf',
            'alias' => ['cupa fgf']
        ],
        //endregion
        //region bulgaria data
        [
            'id' => 50,
            'country_id' => 28,
            'country_name' => 'bulgaria',
            'name' => '1 castigator',
            'alias' => ['1 castigator']
        ],
        [
            'id' => 51,
            'country_id' => 28,
            'country_name' => 'bulgaria',
            'name' => 'parva liga',
            'alias' => ['parva liga']
        ],
        [
            'id' => 52,
            'country_id' => 28,
            'country_name' => 'bulgaria',
            'name' => 'kupa na bulgaria',
            'alias' => ['kupa na bulgaria']
        ],
        [
            'id' => 53,
            'country_id' => 28,
            'country_name' => 'bulgaria',
            'name' => '2 liga',
            'alias' => ['2 liga', '2 a']
        ],
        //endregion
        //region canada data
        [
            'id' => 54,
            'country_id' => 34,
            'country_name' => 'canada',
            'name' => 'championship',
            'alias' => ['championship']
        ],
        [
            'id' => 55,
            'country_id' => 34,
            'country_name' => 'canada',
            'name' => 'championship',
            'alias' => ['championship']
        ],
        //endregion
        //region cehia data
        [
            'id' => 56,
            'country_id' => 35,
            'country_name' => 'cehia',
            'name' => '1 liga',
            'alias' => ['1 liga', 'cehia 1 ']
        ],
        [
            'id' => 57,
            'country_id' => 35,
            'country_name' => 'cehia',
            'name' => '2 liga',
            'alias' => ['2 liga', 'cehia 2']
        ],
        [
            'id' => 59,
            'country_id' => 35,
            'country_name' => 'cehia',
            'name' => '1 f',
            'alias' => ['1 f']
        ],
        [
            'id' => 60,
            'country_id' => 35,
            'country_name' => 'cehia',
            'name' => '1 clasare finala',
            'alias' => ['1 clasare finala']
        ],
        [
            'id' => 61,
            'country_id' => 35,
            'country_name' => 'cehia',
            'name' => '1 golgheter',
            'alias' => ['1 golgheter']
        ],
        [
            'id' => 62,
            'country_id' => 35,
            'country_name' => 'cehia',
            'name' => '1 f castigator',
            'alias' => ['1 f castigator']
        ],
        [
            'id' => 63,
            'country_id' => 35,
            'country_name' => 'cehia',
            'name' => '2 castigator',
            'alias' => ['2 castigator']
        ],
        //endregion
        //region chile data
        [
            'id' => 64,
            'country_id' => 36,
            'country_name' => 'chile',
            'name' => 'primera division',
            'alias' => ['primera division' , 'chile 1']
        ],
        [
            'id' => 65,
            'country_id' => 36,
            'country_name' => 'chile',
            'name' => 'copa chile',
            'alias' => ['copa chile']
        ],
        [
            'id' => 66,
            'country_id' => 36,
            'country_name' => 'chile',
            'name' => 'primera b',
            'alias' => ['primera b', 'chile 2']
        ],
        //endregion
        //region china data
        [
            'id' => 69,
            'country_id' => 37,
            'country_name' => 'china',
            'name' => 'super league',
            'alias' => ['super league']
        ],
        //endregion
        //region cipru data
        [
            'id' => 71,
            'country_id' => 38,
            'country_name' => 'cipru',
            'name' => 'divizia 1',
            'alias' => ['divizia 1', 'cipru 1']
        ],
        [
            'id' => 72,
            'country_id' => 38,
            'country_name' => 'cipru',
            'name' => 'super cupa',
            'alias' => ['super cupa']
        ],
        [
            'id' => 73,
            'country_id' => 38,
            'country_name' => 'cipru',
            'name' => '1 divizia',
            'alias' => ['1 divizia']
        ],
        //endregion
        //region columbia data
        [
            'id' => 74,
            'country_id' => 39,
            'country_name' => 'columbia',
            'name' => 'primera a clausura',
            'alias' => ['primera a clausura', 'columbia 1']
        ],
        [
            'id' => 75,
            'country_id' => 39,
            'country_name' => 'columbia',
            'name' => 'cupa',
            'alias' => ['cupa']
        ],
        [
            'id' => 76,
            'country_id' => 39,
            'country_name' => 'columbia',
            'name' => 'primera b clausura',
            'alias' => ['primera b clausura', 'columbia 2']
        ],
        [
            'id' => 77,
            'country_id' => 39,
            'country_name' => 'columbia',
            'name' => 'categoria primera a',
            'alias' => ['categoria primera a']
        ],
        [
            'id' => 78,
            'country_id' => 39,
            'country_name' => 'columbia',
            'name' => 'copa colombia',
            'alias' => ['copa colombia']
        ],
        [
            'id' => 79,
            'country_id' => 39,
            'country_name' => 'columbia',
            'name' => 'categoria primera b',
            'alias' => ['categoria primera b']
        ],
        [
            'id' => 81,
            'country_id' => 39,
            'country_name' => 'columbia',
            'name' => 'cupa',
            'alias' => ['cupa']
        ],
    //endregion
        //region coreea de sud data
        [
            'id' => 82,
            'country_id' => 44,
            'country_name' => 'coreea de sud',
            'name' => 'k league 1',
            'alias' => ['k league 1']
        ],
        [
            'id' => 83,
            'country_id' => 44,
            'country_name' => 'coreea de sud',
            'name' => 'k league 2',
            'alias' => ['k league 2']
        ],
        [
            'id' => 84,
            'country_id' => 44,
            'country_name' => 'coreea de sud',
            'name' => 'k league 3',
            'alias' => ['k league 3']
        ],
        [
            'id' => 85,
            'country_id' => 44,
            'country_name' => 'coreea de sud',
            'name' => 'k league f',
            'alias' => ['k league f']
        ],
    //endregion
        //region costa rica data
        [
            'id' => 86,
            'country_id' => 45,
            'country_name' => 'costa rica',
            'name' => 'liga fpd',
            'alias' => ['liga fpd', 'costa rica 1']
        ],
        [
            'id' => 87,
            'country_id' => 45,
            'country_name' => 'costa rica',
            'name' => 'primera division apertura',
            'alias' => ['primera division apertura', 'costa rica 1']
        ],
    //endregion
        //region croatia data
        [
            'id' => 88,
            'country_id' => 47,
            'country_name' => 'croatia',
            'name' => 'hnl',
            'alias' => ['hnl', 'croatia 1']
        ],
    //endregion
        //region danemarca data
        [
            'id' => 92,
            'country_id' => 49,
            'country_name' => 'danemarca',
            'name' => 'superligaen',
            'alias' => ['superligaen']
        ],
        [
            'id' => 93,
            'country_id' => 49,
            'country_name' => 'danemarca',
            'name' => 'dbu pokalen',
            'alias' => ['dbu pokalen']
        ],
        [
            'id' => 94,
            'country_id' => 49,
            'country_name' => 'danemarca',
            'name' => '1 division',
            'alias' => ['1 division']
        ],
        [
            'id' => 95,
            'country_id' => 49,
            'country_name' => 'danemarca',
            'name' => '2 division',
            'alias' => ['2 division']
        ],
        [
            'id' => 96,
            'country_id' => 49,
            'country_name' => 'danemarca',
            'name' => '3 division',
            'alias' => ['3 division']
        ],
        //endregion
        //region ecuador data
        [
            'id' => 97,
            'country_id' => 53,
            'country_name' => 'ecuador',
            'name' => 'serie a',
            'alias' => ['serie a']
        ],
        [
            'id' => 98,
            'country_id' => 53,
            'country_name' => 'ecuador',
            'name' => 'cupa',
            'alias' => ['cupa']
        ],
        [
            'id' => 99,
            'country_id' => 53,
            'country_name' => 'ecuador',
            'name' => 'serie b',
            'alias' => ['serie b']
        ],
        [
            'id' => 100,
            'country_id' => 53,
            'country_name' => 'ecuador',
            'name' => 'liga pro serie a',
            'alias' => ['liga pro serie a']
        ],
        [
            'id' => 101,
            'country_id' => 53,
            'country_name' => 'ecuador',
            'name' => 'copa',
            'alias' => ['copa']
        ],
        [
            'id' => 102,
            'country_id' => 53,
            'country_name' => 'ecuador',
            'name' => 'liga pro serie b',
            'alias' => ['liga pro serie b']
        ],
        [
            'id' => 104,
            'country_id' => 53,
            'country_name' => 'ecuador',
            'name' => 'cupa',
            'alias' => ['cupa']
        ],
        //endregion
        //region el salvador data
        [
            'id' => 106,
            'country_id' => 55,
            'country_name' => 'el salvador',
            'name' => 'primera division',
            'alias' => ['primera division']
        ],
        [
            'id' => 107,
            'country_id' => 55,
            'country_name' => 'el salvador',
            'name' => 'primera division apertura',
            'alias' => ['primera division apertura']
        ],
        //endregion
        //region emiratatele arabe unite data
        [
            'id' => 108,
            'country_id' => 56,
            'country_name' => 'emiratele arabe unite',
            'name' => 'pro league',
            'alias' => ['pro league']
        ],
        //endregion
        //region estonia data
        [
            'id' => 109,
            'country_id' => 58,
            'country_name' => 'estonia',
            'name' => 'premium liiga',
            'alias' => ['premium liiga', 'estonia 1']
        ],
        [
            'id' => 111,
            'country_id' => 58,
            'country_name' => 'estonia',
            'name' => 'meistriliiga',
            'alias' => ['meistriliiga']
        ],
    //endregion
        //region etiopia data
        [
            'id' => 112,
            'country_id' => 60,
            'country_name' => 'etiopia',
            'name' => 'premier league',
            'alias' => ['premier league']
        ],
    //endregion
        //region finlanda data
        [
            'id' => 114,
            'country_id' => 63,
            'country_name' => 'finlanda',
            'name' => 'veikkausliiga',
            'alias' => ['veikkausliiga']
        ],
        [
            'id' => 115,
            'country_id' => 63,
            'country_name' => 'finlanda',
            'name' => 'ykkosliiga',
            'alias' => ['ykkosliiga']
        ],
        [
            'id' => 116,
            'country_id' => 63,
            'country_name' => 'finlanda',
            'name' => 'kolmonen',
            'alias' => ['kolmonen']
        ],
        [
            'id' => 119,
            'country_id' => 63,
            'country_name' => 'finlanda',
            'name' => 'veikkausliiga grcampionat',
            'alias' => ['veikkausliiga grcampionat']
        ],
        [
            'id' => 120,
            'country_id' => 63,
            'country_name' => 'finlanda',
            'name' => 'veikkausliiga grretrogradare',
            'alias' => ['veikkausliiga grretrogradare']
        ],
        //endregion
        //region franta data
        [
            'id' => 121,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'clasare finala',
            'alias' => ['clasare finala']
        ],
        [
            'id' => 122,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'clasare duel',
            'alias' => ['clasare duel']
        ],
        [
            'id' => 123,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'golgheter',
            'alias' => ['golgheter']
        ],
        [
            'id' => 124,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'national 1',
            'alias' => ['national 1']
        ],
        [
            'id' => 125,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'ligue 1',
            'alias' => ['ligue 1', 'franta ligue 1']
        ],
        [
            'id' => 126,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'coupe de france',
            'alias' => ['coupe de france']
        ],
        [
            'id' => 127,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'ligue 2',
            'alias' => ['ligue 2', 'franta 2']
        ],
        [
            'id' => 128,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'national',
            'alias' => ['national']
        ],
        //endregion
        //region georgia data
        [
            'id' => 130,
            'country_id' => 67,
            'country_name' => 'georgia',
            'name' => 'erovnuli liga',
            'alias' => ['erovnuli liga', 'georgia 1']
        ],
        //endregion
        //region germania data
        [
            'id' => 132,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'bundesliga',
            'alias' => ['bundesliga', 'germania bundesliga', 'germania 1','germania 1 (f)']
        ],
        [
            'id' => 133,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'dfb pokal',
            'alias' => ['dfb pokal']
        ],
        [
            'id' => 134,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => '2 bundesliga',
            'alias' => ['2 bundesliga', 'germania 2']
        ],
        [
            'id' => 135,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => '3 liga',
            'alias' => ['3 liga', 'germania 3']
        ],
        [
            'id' => 136,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'niederrheinpokal',
            'alias' => ['niederrheinpokal']
        ],
        [
            'id' => 137,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'cupa',
            'alias' => ['cupa' , 'germania cupa']
        ],
        [
            'id' => 138,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'bundesliga clasare finala',
            'alias' => ['bundesliga clasare finala']
        ],
        [
            'id' => 139,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'bundesliga clasare duel',
            'alias' => ['bundesliga clasare duel']
        ],
        [
            'id' => 140,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'bundesliga golgheter',
            'alias' => ['bundesliga golgheter']
        ],
//endregion
        //region grecia data
//        [
//            'id' => 141,
//            'country_id' => 70,
//            'country_name' => 'grecia',
//            'name' => '1 castigator',
//            'alias' => ['1 castigator']
//        ],
        [
            'id' => 142,
            'country_id' => 70,
            'country_name' => 'grecia',
            'name' => 'super league',
            'alias' => ['super league', 'grecia 1']
        ],
        [
            'id' => 143,
            'country_id' => 70,
            'country_name' => 'grecia',
            'name' => 'cupa',
            'alias' => ['cupa']
        ],
        [
            'id' => 144,
            'country_id' => 70,
            'country_name' => 'grecia',
            'name' => 'super league 2 grb',
            'alias' => ['super league 2 grb', 'grecia 2']
        ],
        //endregion
        //region guatemala data
        [
            'id' => 148,
            'country_id' => 72,
            'country_name' => 'guatemala',
            'name' => 'liga nacional',
            'alias' => ['liga nacional']
        ],
        [
            'id' => 149,
            'country_id' => 72,
            'country_name' => 'guatemala',
            'name' => 'primera division de ascenso',
            'alias' => ['primera division de ascenso']
        ],
        [
            'id' => 150,
            'country_id' => 72,
            'country_name' => 'guatemala',
            'name' => 'segunda division',
            'alias' => ['segunda division']
        ],
        [
            'id' => 151,
            'country_id' => 72,
            'country_name' => 'guatemala',
            'name' => 'liga nacional apertura',
            'alias' => ['liga nacional apertura']
        ],
        //endregion
        //region honduras data
        [
            'id' => 152,
            'country_id' => 78,
            'country_name' => 'honduras',
            'name' => 'reserve league',
            'alias' => ['reserve league']
        ],
        //endregion
        //region india data
        [
            'id' => 153,
            'country_id' => 79,
            'country_name' => 'india',
            'name' => 'super league',
            'alias' => ['super league']
        ],
        [
            'id' => 154,
            'country_id' => 79,
            'country_name' => 'india',
            'name' => 'sikkim premier division league',
            'alias' => ['sikkim premier division league']
        ],
        [
            'id' => 155,
            'country_id' => 79,
            'country_name' => 'india',
            'name' => 'mumbai super division',
            'alias' => ['mumbai super division']
        ],
        [
            'id' => 156,
            'country_id' => 79,
            'country_name' => 'india',
            'name' => 'i league 3',
            'alias' => ['i league 3']
        ],
        [
            'id' => 157,
            'country_id' => 79,
            'country_name' => 'india',
            'name' => 'super league',
            'alias' => ['super league']
        ],
//endregion
        //region indonezia data
        [
            'id' => 158,
            'country_id' => 80,
            'country_name' => 'indonezia',
            'name' => 'liga 1',
            'alias' => ['liga 1', 'indonezia 1']
        ],
        [
            'id' => 159,
            'country_id' => 80,
            'country_name' => 'indonezia',
            'name' => 'liga 2',
            'alias' => ['liga 2', 'indonezia 2']
        ],
        [
            'id' => 160,
            'country_id' => 80,
            'country_name' => 'indonezia',
            'name' => 'super league',
            'alias' => ['super league']
        ],
//endregion
        //region irak data
        [
            'id' => 161,
            'country_id' => 81,
            'country_name' => 'irak',
            'name' => 'iraqi league',
            'alias' => ['iraqi league']
        ],
//endregion
        //region iran data
        [
            'id' => 164,
            'country_id' => 82,
            'country_name' => 'iran',
            'name' => 'pro league',
            'alias' => ['pro league', 'persian gulf pro league', 'iran 1']
        ],
//endregion
        //region irlanda data
        [
            'id' => 165,
            'country_id' => 83,
            'country_name' => 'irlanda',
            'name' => 'first division',
            'alias' => ['first division', 'prima divizie', 'irlanda 1']
        ],
        [
            'id' => 166,
            'country_id' => 83,
            'country_name' => 'irlanda',
            'name' => 'premier division',
            'alias' => ['premier division']
        ],
//        [
//            'id' => 167,
//            'country_id' => 83,
//            'country_name' => 'irlanda',
//            'name' => 'prima divizie',
//            'alias' => ['prima divizie']
//        ],
        [
            'id' => 168,
            'country_id' => 83,
            'country_name' => 'irlanda',
            'name' => 'munster senior league',
            'alias' => ['munster senior league']
        ],
        [
            'id' => 169,
            'country_id' => 83,
            'country_name' => 'irlanda',
            'name' => 'national league f',
            'alias' => ['national league f']
        ],
        [
            'id' => 171,
            'country_id' => 83,
            'country_name' => 'irlanda',
            'name' => 'de nord 1',
            'alias' => ['de nord 1', 'irlanda de nord 1']
        ],
//endregion
        //region islanda data
        [
            'id' => 172,
            'country_id' => 84,
            'country_name' => 'islanda',
            'name' => 'urvalsdeild',
            'alias' => ['urvalsdeild', 'islanda 1']
        ],
        [
            'id' => 173,
            'country_id' => 84,
            'country_name' => 'islanda',
            'name' => '2 flokkur u19',
            'alias' => ['2 flokkur u19']
        ],
        [
            'id' => 175,
            'country_id' => 84,
            'country_name' => 'islanda',
            'name' => 'urvalsdeild gr campionat',
            'alias' => ['urvalsdeild gr campionat']
        ],
        [
            'id' => 176,
            'country_id' => 84,
            'country_name' => 'islanda',
            'name' => 'urvalsdeild gr retrogradare',
            'alias' => ['urvalsdeild gr retrogradare']
        ],
//endregion
        //region israel data
        [
            'id' => 177,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'premier league',
            'alias' => ['premier league']
        ],
        [
            'id' => 178,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'ligat haal',
            'alias' => ['ligat haal' , 'israel 1']
        ],
        [
            'id' => 179,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'state cup',
            'alias' => ['state cup']
        ],
        [
            'id' => 180,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'liga leumit',
            'alias' => ['liga leumit', 'israel 2']
        ],
        [
            'id' => 181,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'toto cup ligat leumit',
            'alias' => ['toto cup ligat leumit']
        ],
        [
            'id' => 182,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'liga alef',
            'alias' => ['liga alef']
        ],
        [
            'id' => 183,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'liga bet',
            'alias' => ['liga bet']
        ],
        [
            'id' => 184,
            'country_id' => 85,
            'country_name' => 'israel',
            'name' => 'ligat al f',
            'alias' => ['ligat al f']
        ],
//        [
//            'id' => 185,
//            'country_id' => 85,
//            'country_name' => 'israel',
//            'name' => '2',
//            'alias' => ['2']
//        ],
        //endregion
        //region italia !! nou are dublat id( am pus 0)
        [
            'id' => 1850,
            'country_id' => 86,
            'country_name' => 'italia',
            'name' => 'serie a',
            'alias' => ['serie a', 'italia serie a']
        ],
        //endregion
        //region polonia data
        [
            'id' => 186,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => 'ekstraklasa',
            'alias' => ['ekstraklasa']
        ],
        [
            'id' => 187,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => 'puchar polski',
            'alias' => ['puchar polski', 'cupa']
        ],
        [
            'id' => 188,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => 'i liga',
            'alias' => ['i liga', 'polonia 1']
        ],
        [
            'id' => 189,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => 'ii liga',
            'alias' => ['ii liga', 'polonia 2']
        ],
        [
            'id' => 190,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => '3_0',
            'alias' => ['3_0', 'polonia 3']
        ],
        [
            'id' => 191,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => '4 gr i',
            'alias' => ['4 gr i', 'polonia 4']
        ],
        [
            'id' => 192,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => '4 gr ii',
            'alias' => ['4 gr ii', 'polonia 4']
        ],
        [
            'id' => 193,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => '4 gr iii',
            'alias' => ['4 gr iii', 'polonia 4']
        ],
        [
            'id' => 194,
            'country_id' => 137,
            'country_name' => 'polonia',
            'name' => '4 gr iv',
            'alias' => ['4 gr iv', 'polonia 4']
        ],
        //endregion
        //region portugalia data
        [
            'id' => 195,
            'country_id' => 138,
            'country_name' => 'portugalia',
            'name' => 'primeira liga',
            'alias' => ['primeira liga', 'liga 1', 'portugalia 1']
        ],
        [
            'id' => 196,
            'country_id' => 138,
            'country_name' => 'portugalia',
            'name' => 'liga 2',
            'alias' => ['liga 2', 'portugalia 2']
        ],
        [
            'id' => 197,
            'country_id' => 138,
            'country_name' => 'portugalia',
            'name' => 'liga 3',
            'alias' => ['liga 3', 'portugalia 3']
        ],
        [
            'id' => 198,
            'country_id' => 138,
            'country_name' => 'portugalia',
            'name' => 'liga 3 grb',
            'alias' => ['liga 3 grb', 'portugalia 3']
        ],
        [
            'id' => 199,
            'country_id' => 138,
            'country_name' => 'portugalia',
            'name' => '1 castigator',
            'alias' => ['1 castigator']
        ],
        //endregion
        //region qatar data
        [
            'id' => 200,
            'country_id' => 139,
            'country_name' => 'qatar',
            'name' => 'stars league',
            'alias' => ['stars league', 'liga 1', 'qatar 1']
        ],
        //endregion
        //region romania data
        [
            'id' => 201,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'liga 1',
            'alias' => ['liga 1', 'romania 1', 'superliga']
        ],
        [
            'id' => 202,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'liga 2',
            'alias' => ['liga 2', 'romania 2']
        ],
        [
            'id' => 203,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'cupa romaniei betano',
            'alias' => ['cupa romaniei betano', 'romania cupa']
        ],
        [
            'id' => 204,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => '1 clasare finala',
            'alias' => ['1 clasare finala']
        ],
        [
            'id' => 205,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => '1 duel clasare',
            'alias' => ['1 duel clasare']
        ],
        [
            'id' => 206,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => '1 retrogradeaza direct',
            'alias' => ['1 retrogradeaza direct']
        ],
        [
            'id' => 207,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'cupa gra',
            'alias' => ['cupa gra']
        ],
        [
            'id' => 208,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'cupa grb',
            'alias' => ['cupa grb']
        ],
        [
            'id' => 209,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'cupa grc',
            'alias' => ['cupa grc']
        ],
        [
            'id' => 210,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'cupa grd',
            'alias' => ['cupa grd']
        ],
        //endregion
        //region rusia data
        [
            'id' => 211,
            'country_id' => 141,
            'country_name' => 'rusia',
            'name' => 'premier league',
            'alias' => ['premier league', 'rusia 1']
        ],
        [
            'id' => 212,
            'country_id' => 141,
            'country_name' => 'rusia',
            'name' => 'fnl',
            'alias' => ['fnl', 'rusia 2']
        ],
        [
            'id' => 213,
            'country_id' => 141,
            'country_name' => 'rusia',
            'name' => 'cupa rpl path playoff',
            'alias' => ['cupa rpl path playoff', 'cupa']
        ],
        //endregion
        //region san marino data
        [
            'id' => 214,
            'country_id' => 145,
            'country_name' => 'san marino',
            'name' => 'coppa titano',
            'alias' => ['coppa titano', 'cupa']
        ],
        //endregion
        //region serbia data
        [
            'id' => 215,
            'country_id' => 147,
            'country_name' => 'serbia',
            'name' => 'superliga',
            'alias' => ['superliga', 'super liga', 'liga 1', 'serbia liga 1']
        ],
        //endregion
        //region spania ! nou are dublat id( am pus 0, 1) in coada
        [
            'id' => 2150,
            'country_id' => 185,
            'country_name' => 'spania',
            'name' => 'supercupa',
            'alias' => ['supercupa', 'spania supercupa']
        ],
        [
            'id' => 2151,
            'country_id' => 185,
            'country_name' => 'spania',
            'name' => 'la liga',
            'alias' => ['la liga', 'spania la liga']
        ],
        //endregion
        //region slovacia data
        [
            'id' => 216,
            'country_id' => 152,
            'country_name' => 'slovacia',
            'name' => '1 liga',
            'alias' => ['1 liga', 'liga 1', 'slovacia 1']
        ],
        [
            'id' => 217,
            'country_id' => 152,
            'country_name' => 'slovacia',
            'name' => '2 liga',
            'alias' => ['2 liga', 'liga 2', 'slovacia 2']
        ],
        [
            'id' => 218,
            'country_id' => 152,
            'country_name' => 'slovacia',
            'name' => 'superliga',
            'alias' => ['superliga']
        ],
        //endregion
        //region slovenia data
        [
            'id' => 219,
            'country_id' => 153,
            'country_name' => 'slovenia',
            'name' => 'prvaliga',
            'alias' => ['prvaliga', '1 liga', 'slovenia 1']
        ],
        [
            'id' => 220,
            'country_id' => 153,
            'country_name' => 'slovenia',
            'name' => '2 snl',
            'alias' => ['2 snl', 'liga 2', 'slovenia 2']
        ],
        //endregion
        //region suedia data
        [
            'id' => 221,
            'country_id' => 157,
            'country_name' => 'suedia',
            'name' => 'allsvenskan',
            'alias' => ['allsvenskan', 'suedia 1']
        ],
        [
            'id' => 222,
            'country_id' => 157,
            'country_name' => 'suedia',
            'name' => 'superettan',
            'alias' => ['superettan', 'suedia 2']
        ],
        //endregion
        //region tanzania data
        [
            'id' => 223,
            'country_id' => 163,
            'country_name' => 'tanzania',
            'name' => 'premier league',
            'alias' => ['premier league', 'tanzania 1']
        ],
        //endregion
        //region thailanda data
        [
            'id' => 224,
            'country_id' => 164,
            'country_name' => 'thailanda',
            'name' => 'thai league 1',
            'alias' => ['thai league 1', 'thailanda 1']
        ],
        //endregion
        //region tunisia data
        [
            'id' => 225,
            'country_id' => 169,
            'country_name' => 'tunisia',
            'name' => 'ligue 1',
            'alias' => ['ligue 1', 'tunisia 1', 'liga i']
        ],
        //endregion
        //region turcia data
        [
            'id' => 226,
            'country_id' => 170,
            'country_name' => 'turcia',
            'name' => 'super lig',
            'alias' => ['super lig', 'liga 1', 'turcia 1']
        ],
        [
            'id' => 227,
            'country_id' => 170,
            'country_name' => 'turcia',
            'name' => 'tff 1 lig',
            'alias' => ['tff 1 lig', '1 lig', 'turcia 2']
        ],
    //endregion
        //region ucraina data
        [
            'id' => 228,
            'country_id' => 173,
            'country_name' => 'ucraina',
            'name' => 'premier liga',
            'alias' => ['premier liga', 'premier league', 'ucraina 1']
        ],
        //endregion
        //region ungaria data
        [
            'id' => 229,
            'country_id' => 175,
            'country_name' => 'ungaria',
            'name' => 'nb i',
            'alias' => ['nb i', 'liga 1', 'ungaria 1']
        ],
        //endregion
        //region uruguay data
        [
            'id' => 230,
            'country_id' => 176,
            'country_name' => 'uruguay',
            'name' => 'primera division',
            'alias' => ['primera division', 'liga 1', 'uruguay 1']
        ],
        [
            'id' => 231,
            'country_id' => 176,
            'country_name' => 'uruguay',
            'name' => 'segunda division',
            'alias' => ['segunda division', 'liga 2', 'uruguay 2']
        ],
        [
            'id' => 232,
            'country_id' => 176,
            'country_name' => 'uruguay',
            'name' => 'cupa',
            'alias' => ['cupa', 'copa auf']
        ],
        //endregion
        //region uzbekistan data
        [
            'id' => 233,
            'country_id' => 177,
            'country_name' => 'uzbekistan',
            'name' => 'superligasi',
            'alias' => ['superligasi', 'super league', 'uzbekistan 1']
        ],
        //endregion
        //region venezuela data
        [
            'id' => 234,
            'country_id' => 180,
            'country_name' => 'venezuela',
            'name' => 'primera division',
            'alias' => ['primera division', 'liga 1', 'venezuela 1']
        ],
        //endregion

        //region competition between countries
        [
            'id' => 235,
            'country_id' => null,
            'country_name' => null,
            'name' => 'liga natiunilor',
            'alias' => ['liga natiunilor']
        ],
        [
            'id' => 236,
            'country_id' => null,
            'country_name' => null,
            'name' => 'calificari cm america de sud',
            'alias' => ['calificari cm america de sud']
        ],
        [
            'id' => 237,
            'country_id' => null,
            'country_name' => null,
            'name' => 'liga campionilor',
            'alias' => ['liga campionilor']
        ],
        [
            'id' => 238,
            'country_id' => null,
            'country_name' => null,
            'name' => 'europa league',
            'alias' => ['europa league']
        ],
        [
            'id' => 239,
            'country_id' => null,
            'country_name' => null,
            'name' => 'conference league',
            'alias' => ['conference league']
        ],
        //endregion
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competitions = $this->competitions;
        if (Competition::count() == 0) {
            foreach ($competitions as $competition) {
                Competition::create([
                    'name' => $competition['name'],
                    'country_id' => $competition['country_id'],
                    'alias' => json_encode($competition['alias'])
                ]);
            }
        }
    }
}
