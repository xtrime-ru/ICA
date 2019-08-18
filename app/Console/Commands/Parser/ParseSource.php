<?php

namespace App\Console\Commands\Parser;

use App\Parsers\ParserFabric;
use App\Console\Commands\Command;

class ParseSource extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:source {sourceId}';

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
        $sourceId = (int) $this->argument('sourceId');
        $parser = ParserFabric::get($sourceId);
        $parser->run();
    }
}
