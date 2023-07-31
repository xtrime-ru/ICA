<?php

namespace App\Console\Commands\Parser;

use App\Models\Post;
use App\Models\Source;
use App\Parsers\ParserFabric;
use App\Console\Commands\Command;
use Illuminate\Support\Facades\Log;

class ParseSource extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:sources {--id=} {--type=} {--access=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run parsing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** @var Source $sources */
        $sources = Source::query();

        if ($id = (int) $this->option('id')) {
            $sources->whereId($id);
        }
        if ($type = (string) $this->option('type')) {
            $sources->whereType($type);
        }
        if ($access = (string) $this->option('access')) {
            $sources->whereAccess($access);
        }

        $totalPosts = 0;
        $totalSources = 0;
        $errors = [];
        foreach ($sources->get() as $source) {
            if (!$source || !$source->exists) {
                throw new \Exception('Not found source id: ' . $id);
            }

            try {
                $parser = ParserFabric::get($source);
                $posts = $parser->getPosts();
            } catch (\Throwable $e) {
                Log::error($e);
                $errors[] = $source->id;
                continue;
            }

            $created = 0;
            $updated = 0;
            $postIds = [];
            foreach ($posts as $post) {
                if (!$post instanceof Post) {
                    Log::error('post', ['post' => $post]);
                    throw new \UnexpectedValueException('Wrong type of post');
                }
                Log::debug('post', ['post' => $post->toArray()]);
                if (!$post->exists) {
                    $created++;
                } else {
                    $updated++;
                }

                $post->save();

                $postIds[] = $post->id;
            }

            $source->posts()->syncWithoutDetaching($postIds);

            Log::info('Saved source: ', [
                'source_id' => $source->id,
                'source_url' => $source->parser_url,
                'created' => $created,
                'updated' => $updated,
                'total' => count($posts),
            ]);
            $totalPosts += count($posts);
            $totalSources++;
        }

        Log::info('Summary: ', [
            'sources count' => $totalSources,
            'posts found' => $totalPosts,
            'errors' => $errors,
        ]);
    }
}
