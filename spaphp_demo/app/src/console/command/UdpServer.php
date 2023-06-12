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
use spaphp\socket\Socket;
use spaphp\socket\transport\Udp;

class UdpServer extends Command
{

    protected function config()
    {
        $this->setName('udp_server');

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

        $this->setDescription('Just a udp server');
    }

    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $debugMode = $input->has(['--debug', '-d'], true);

        // udp server
        $udpServer = new Socket('udp://0.0.0.0:1231');
        $udpServer->num = 2;

        $udpServer->on('data', function (Udp $connection, $data) {
            echo "hi $data \n";
        });

        $manager = Manager::getInstance();
        $manager->monitoredFiles = [
        ];
        $manager->setName($this->name);
        $manager->setCommand($action);
        $manager->setDebug($debugMode);

        $manager->addWorker($udpServer);

        $manager->run();
    }
}
