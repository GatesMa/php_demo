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

class WebSocketServer extends Command
{

    protected function config()
    {
        $this->setName('websocket_server');

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

        $this->setDescription('Just a websocket server');
    }

    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        $debugMode = $input->has(['--debug', '-d'], true);

        // websocket server
        $webSocket = new Socket("websocket://0.0.0.0:1233");

        $webSocket->on('connect', function($connection){
            echo "new connection\n";
        });
        $webSocket->on('data', function(Tcp $connection, $data){
            // Send hello $data
            echo "recv: $data\n";
            $connection->send('hello ' . $data);
        });

        $webSocket->on('close', function($connection){
            echo "connection closed\n";
        });

        $manager = Manager::getInstance();
        $manager->setName($this->name);
        $manager->setCommand($action);
        $manager->setDebug($debugMode);

        $manager->addWorker($webSocket);

        $manager->run();
    }
}
