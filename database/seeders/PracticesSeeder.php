<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PracticesSeeder extends Seeder
{
    public function run(): void
    {
        $practices = [
            ['slug' => 'vanilla',       'name_en' => 'Vanilla / Classic',          'name_de' => 'Vanilla / Klassisch',           'sort_order' => 1],
            ['slug' => 'oral',          'name_en' => 'Oral',                        'name_de' => 'Oral',                          'sort_order' => 2],
            ['slug' => 'anal',          'name_en' => 'Anal',                        'name_de' => 'Anal',                          'sort_order' => 3],
            ['slug' => 'bdsm',          'name_en' => 'BDSM (Bondage & Discipline)', 'name_de' => 'BDSM (Bondage & Disziplin)',    'sort_order' => 4],
            ['slug' => 'domination',    'name_en' => 'Dominance & Submission',      'name_de' => 'Dominanz & Unterwerfung',       'sort_order' => 5],
            ['slug' => 'roleplay',      'name_en' => 'Role Play',                   'name_de' => 'Rollenspiel',                   'sort_order' => 6],
            ['slug' => 'tantric',       'name_en' => 'Tantric / Slow',              'name_de' => 'Tantra / Langsam',              'sort_order' => 7],
            ['slug' => 'toys',          'name_en' => 'Toys & Accessories',          'name_de' => 'Toys & Accessoires',            'sort_order' => 8],
            ['slug' => 'exhibitionism', 'name_en' => 'Exhibitionism / Outdoor',     'name_de' => 'Exhibitionismus / Outdoor',     'sort_order' => 9],
            ['slug' => 'group',         'name_en' => 'Threesome / Group',           'name_de' => 'Dreier / Gruppe',               'sort_order' => 10],
            ['slug' => 'swinging',      'name_en' => 'Swinging',                    'name_de' => 'Swingen',                       'sort_order' => 11],
            ['slug' => 'fetish',        'name_en' => 'Fetish (Foot, Lingerie ...)', 'name_de' => 'Fetisch (Fuß, Lingerie ...)',   'sort_order' => 12],
            ['slug' => 'sexting',       'name_en' => 'Sexting / Digital only',      'name_de' => 'Sexting / Nur digital',         'sort_order' => 13],
            ['slug' => 'fwb',           'name_en' => 'Friends with Benefits',       'name_de' => 'Freunde mit Extras',            'sort_order' => 14],
            ['slug' => 'ons',           'name_en' => 'One Night Stand',             'name_de' => 'One Night Stand',               'sort_order' => 15],
        ];
        foreach ($practices as $p) {
            DB::table('practices')->updateOrInsert(['slug' => $p['slug']], array_merge($p, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }
    }
}
