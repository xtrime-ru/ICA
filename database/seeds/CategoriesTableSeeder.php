<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    private static $categoriesJson = '
    [
        {
            "id": 1,
            "name": "Развлечения",
            "slug": "fun",
            "color": "#E47387"
        },
        {
            "id": 2,
            "name": "Игры",
            "slug": "games",
            "color": "#FFCF81"
        },
        {
            "id": 3,
            "name": "Кино и музыка",
            "slug": "arts",
            "color": "#FEFF81"
        },
        {
            "id": 4,
            "name": "Новости",
            "slug": "news",
            "color": "#C4FF81"
        },
        {
            "id": 5,
            "name": "IT",
            "slug": "it",
            "color": "#81E9FF"
        },
        {
            "id": 6,
            "name": "Наука",
            "slug": "science",
            "color": "#81B7FF"
        },
        {
            "id": 7,
            "name": "LifeStyle",
            "slug": "lifestyle",
            "color": "#C281FF"
        },
        {
            "id": 8,
            "name": "Women",
            "slug": "women",
            "color": "#FF81E9"
        },
        {
            "id": 9,
            "name": "Авто",
            "slug": "auto",
            "color": "#FF45CF"
        },
        {
            "id": 10,
            "name": "Спорт",
            "slug": "sports",
            "color": "#EF233C"
        },
        {
            "id": 11,
            "name": "Блоги",
            "slug": "blogs",
            "color": "#04E561"
        },
        {
            "id": 12,
            "name": "Криптовалюты",
            "slug": "crypto",
            "color": "#C4FF81"
        }
    ]
    ';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = json_decode(static::$categoriesJson, true);

        foreach ($categories as $category) {
            \App\Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'style' => ['color' => $category['color']],
            ])->save();
        }
    }
}
