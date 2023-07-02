<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;
use Nette\Utils\Json;

class SourcesTableSeeder extends Seeder
{
    private static function getSources() {
        $pointer = fopen(__DIR__ . '/sources_seed.csv', 'rb');

        $columns = [];
        while(!feof($pointer)) {
            $row = fgetcsv($pointer);
            if ($row) {
                if (!$columns) {
                    $columns = $row;
                } else {
                    yield array_combine($columns, $row);
                }
            }
        }

        fclose($pointer);
    }

    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        foreach (static::getSources() as $data) {
            $source = $data;
            var_dump($data);
            $source['parser_rules'] = Json::encode(
                json_decode($source['parser_rules'], true),
                JSON_PRETTY_PRINT
            );
            $source['next_parse_at'] = strtotime('+10 minutes');
            var_dump($source);
            foreach ($source as $key => $value) {
                if ($value === "\\N" || strtolower($value) === 'null') {
                    $source[$key] = null;
                }
            }

            if ($source['icon']) {
                $source['icon'] = hex2bin(str_replace('0x', '', $source['icon']));
            }

            Source::create($source);
        }
    }
}
