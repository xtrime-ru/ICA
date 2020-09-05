<?php

use App\Models\Source;
use Illuminate\Database\Seeder;
use Psy\Util\Json;

class SourcesTableSeeder extends Seeder
{
    private static function getSources() {
        $next_parse_at = strtotime('+10 minutes');

        return [
            [
                'name' =>'Pikabu: Best',
                'category_id' => 1,
                'user_id' => 1,
                'access' => 'public',
                'url' => 'https://pikabu.ru',
                'parser_url' => 'https://pikabu.ru/search.php?r=5',
                'parser_type' => 'html',
                'parser_rules' => '{
                    "posts": ".story:not(:last)",
                    "imgPath": ".story__content img:eq(0), .player__preview",
                    "headerPath": ".story__title a",
                    "textPath": ".story__content",
                    "urlPath": ".story__title a"
                }',
                'parse_interval' => 10,
                'next_parse_at' => $next_parse_at,
            ]
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (static::getSources() as $data) {
            $source = $data;
            $source['parser_rules'] = Json::encode(
                json_decode($source['parser_rules'], true),
                JSON_PRETTY_PRINT
            );
            Source::create($source)->save();
        }
    }
}
