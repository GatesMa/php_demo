## logging

### 特性
按照psr规范，提供日志记录服务；

在psr已有的日志级别基础上，新增了 ACCESS 和 TRACE 两种级别


#### ACCESS
访问日志，每次请求中，有且仅有一条

#### TRACE
跟踪日志，介于 debug 和 info 日志之间的级别

### 用例

#### 使用facade
```
<?php

use spaphp\facade\Log;

Log::error($message, $context);

Log::info($message, $context);

Log::warning($message, $context);

Log::trace($message, $context);

```

#### 使用实例
```
<?php

$log = App::make('log');

$log->error($message, $context);

$log->info($message, $context);

$log->warnging($message, $context);

$log->trace($message, $context);

```