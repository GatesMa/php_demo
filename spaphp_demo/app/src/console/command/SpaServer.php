<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/12/13
 * Time: 下午3:01
 */

namespace app\console\command;

use spaphp\console\Command;
use spaphp\console\contract\Input;
use spaphp\console\contract\Output;
use spaphp\console\InputArgument;
use spaphp\console\InputOption;
use spaphp\socket\Manager;
use spaphp\socket\SpaServer as Server;

class SpaServer extends Command
{

    protected function config()
    {
        $this->setName('spa_server');

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

        $this->setDescription('基于spaphp framework的webserver');
    }

    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $debugMode = $input->has(['--debug', '-d'], true);

        // spaServer
        $spaServer = new Server('http://0.0.0.0:1235');
        $spaServer->reusePort = false;
        $spaServer->num = 20;
        $spaServer->addVhost(
            'abc.com',
            [
                'root' => $this->app->path('web'),
                'index' => 'index.htm index.php',
                'custom404' => null,
            ]
        );

        $manager = Manager::getInstance();
        $manager->setName($this->name);
        $manager->setCommand($action);
        $manager->setDebug($debugMode);

        $manager->addWorker($spaServer);

        $manager->run();
    }
}
