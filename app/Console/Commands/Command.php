<?php


namespace App\Console\Commands;


use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \Illuminate\Console\Command
{
    public function __construct()
    {
        parent::__construct();

        //Что бы не прокидывать в дочерние классы $output перенаправляем все логи в консоль
        //Везде вместо $output используем \Log
        if (posix_isatty(STDOUT)) {
            $this->setLogLevel();
            \Log::setDefaultDriver('stdout');
            \Log::debug('Логи перенаправлены в консоль');
        } else {
            //Non interactive mode
        }
    }

    private function setLogLevel() {
        //Тут еще нет $this->verbosity поэтому получаем сами.
        $verbose = array_intersect(['-v', '-vv', '-vvv', '--verbose', '-q', '--quiet'], $_SERVER['argv']);
        $verbose = end($verbose);
        $verbose = str_replace(['-', 'verbose', 'q'], ['', 'vvv', 'quiet'], $verbose);
        $this->setVerbosity($verbose);

        switch ($this->verbosity) {
            case OutputInterface::VERBOSITY_QUIET:
                $level = 'critical';
                break;
            case OutputInterface::VERBOSITY_NORMAL:
                $level = 'info';
                break;
            default:
                $level = 'debug';
                break;
        }
        \Config::set('logging.channels.stdout.level', $level);
    }
}
