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
use spaphp\socket\WebServer as Server;

class WebServer extends Command
{

    protected function config()
    {
        $this->setName('web_server');

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

        $this->setDescription('Just a web server');
    }

    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $debugMode = $input->has(['--debug', '-d'], true);

        // webserver
        $webServer = new Server('http://0.0.0.0:1234');
        $webServer->reusePort = false;
        $webServer->num = 2;
        $webServer->addVhost(
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

        $manager->addWorker($webServer);

        $manager->run();
    }
}
