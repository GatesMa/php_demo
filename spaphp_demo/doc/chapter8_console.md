# 命令行

<!-- toc -->

## 1. 命令行的使用

SPAPHP命令行的组成结构如下

```shell
php spa [command] [options1] [options2] [arg1] [arg2]
```

所有命令行都以 php spa 为入口文件，后面接上 命令名称，再是类似 -h 这样的一些选项，最后是n个参数

SPAPHP框架的命令行功能中有两个基础命令 help 与 list

首先可以通过 list 命令查看提供了哪些命令行

```shell
php spa list
```

然后再通过 help 命令来查看其中一个命令的用法

```shell
php spa help list
```

## 2. 自定义命令

我们参考一下已有的help命令的写法

```php
class HelpCommand extends Command
{
    protected function config()
    {
        $this->setName('help');
        $this->setDefinition([
            new InputArgument('command_name', InputArgument::OPTIONAL, 'the command name', 'help'),
        ]);

        $this->setDescription('Display help for a command');

        $this->setHelp('<info>%command.name%</info> 显示指定命令的帮助信息:
  <info>php %command.full_name% list</info>

显示所有命令，请使用 <info>list</info> 命令.');
    }

    protected function execute(Input $input, Output $output)
    {
        $command = $this->command ?? $this->getApplication()->find($input->getArgument('command_name'));
        $output->writeln($command->getUsage());

        $this->command = null;
    }
}
```

实现自己的命令只需要 **继承自Command类并重写config与execute方法即可**

其中config方法的内容比较固定，上面的示例中

```php
1. setName设定命令行的名称
2. setDefinition设定该命令接收哪些参数，如下这个定义表示命令接收到一个参数和一个选项，可以通过$input->getArgument('command')取出参数内容
[
    new InputArgument('command', InputArgument::REQUIRED, 'The command to run'),
    new InputOption('--help', '-h', InputOption::NONE_VALUE, 'This help text'),
]
3. setDescription是命令行的描述
4. setHelp是命令行具体的用法
```

execute方法则是和你要实现的命令有关了，这里只介绍取出参数与判断选项

```php
1. 取出参数
$input->getArgument('command_name'); // 这里的command_name根据你在
2. 判断是否添加某个选项
$input->has(['-h'], true); // 判断命令行中是否传递了 -h 选项， 这里第二个bool值表示是否只判断选项的包含
```

## 3. SPAPHP中命令行的实现

SPAPHP命令行的实现位于 spaphp/framework/src/console 目录下

首先有必要搞清楚该目录下各个类之间的关系

```shell
1. Kernel类，接受输入输出，得到结果，作用可类比于 shell
2. Input类与Output类，一次命令执行时输入和输出的封装
3. InputDefinition类，当前输入的参数与选项定义
4. InputArgument类与InputOption类，属于InputDefinition类的组成成员，分别代表参数与选项
5. Command类，命令类的抽象(这里关键点在于Kernel本身的Definition与Command类中的Definition组成了InputDefinition类)
```

下面还是从 php spa help 这条命令的解析入手来分析框架对命令行的实现

最开始是 spa 这个入口文件

```php
#!/usr/bin/env php
<?php
require __DIR__ . '/bin/spa.php';
```

看来真正的入口在spa.php文件中

```php
<?php

// 1. composer 自动加载
require __DIR__ . '/../vendor/autoload.php';

// 2. 引入Application对象，得到项目的定制化容器
$app = require __DIR__ . '/../src/bootstrap/app.php';

// 3. 创建命令行的核心类 Kernel
$kernel = $app->make(Kernel::class);

// 4. 从项目的 src/console/command 目录下加载用户自定义的命令
$kernel->loadCommandsFromPath(
    $app->config->get('app.command.path'),
    $app->config->get('app.command.namespace')
);

// 5. 运行命令
exit($kernel->run(new ArgvInput(), new ConsoleOutput()));
```

Kernel类位于 spaphp/framework/src/console/Kernel.php 当中

它属于命令运行过程中的核心类，主要负责接收输入输出、执行命令、异常处理等工作

接下来  看下Kernel的run方法

```php
public function run(Input $input = null, Output $output = null)
{
    ...
    try {
        $exitCode = $this->doRun($input, $output); // 运行命令行
    } catch (\Throwable $e) {
        ...
    } finally {
        restore_exception_handler();
    }
    return $exitCode;
}
```

最后进入到 Command 类的 run 方法

```php
public function run(Input $input, Output $output)
{
    // 1. 合并默认的参数与选项的配置
    $this->mergeDefinition();

    try {
        // 2. 根据当前的命令的定义解析参数到输入类当中 
        $input->bind($this->definition);
    } catch (Exception $e) {
        throw $e;
    }
    // 3. 运行命令类中用户自定义的execute方法
    $exitCode = $this->execute($input, $output);

    return (int)$exitCode;
}
```

在这个示例 php spa help 当中，最后运行的就是 HelpCommand 中的 execute 方法