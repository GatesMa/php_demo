<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2019/2/25
 * Time: 上午11:36
 */

namespace app\console\command;

use spaphp\console\Command;
use spaphp\console\contract\Input;
use spaphp\console\contract\Output;
use spaphp\console\InputArgument;
use spaphp\console\InputOption;
use spaphp\socket\Manager;
use spaphp\socket\Worker;

class MultiWorker extends Command
{
    protected function config()
    {
        $this->setName('multi_worker');

        $this->setDefinition([
            new InputArgument(
                'action',
                InputArgument::REQUIRED,
                '命令'
            ),
            new InputOption(
                '--debug',
                '-d',
                InputOption::OPTIONAL,
                "是否以 debug 模式运行"
            ),
        ]);

        $this->setDescription('多进程任务模型');
    }


    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $debugMode = $input->has(['--debug', '-d'], true);

        // multi work
        $worker = new Worker();
        $worker->num = 3;

        $worker->on('start', function ($worker) {
            $i = 0;
            while (true) {
//                pcntl_signal_dispatch();

                $i++;
                // do something
                echo "$i, worker_id: " . $worker->id . " do something;\n";

                usleep(1e3);
            }

        });

        $manager = Manager::getInstance();
        $manager->setName($this->name);
        $manager->setCommand($action);
        $manager->setDebug($debugMode);

        $manager->addWorker($worker);

        $manager->run();
    }
}
