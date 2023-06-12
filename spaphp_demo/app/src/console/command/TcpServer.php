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
use spaphp\socket\transport\Tcp;

class TcpServer extends Command
{

    protected function config()
    {
        $this->setName('tcp_server');

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

        $this->setDescription('Just a tcp server');
    }

    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $debugMode = $input->has(['--debug', '-d'], true);

        // tcp server
        $tcpServer = new Socket('tcp://127.0.0.1:1232');
        $tcpServer->num = 3;
        $tcpServer->reusePort = false;
        $tcpServer->on('data', function (Tcp $connection, $data) {
            $connection->send("HTTP/1.1 200 OK\r\nConnection: keep-alive\r\nServer: spaServer\r\nContent-Length: 2\r\n\r\nhi");
        });

        $manager = Manager::getInstance();
        $manager->setName($this->name);
        $manager->setCommand($action);
        $manager->setDebug($debugMode);

        $manager->addWorker($tcpServer);

        $manager->run();
    }
}
