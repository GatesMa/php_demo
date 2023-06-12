# 参数检验

<!-- toc -->

## 1. SPA-PHP参数检验功能

### 1.1 支持的关键词

```php
    'int' // 整型
    'bool' // 布尔型
    'callable' // 可调用
    'array' // 数组型
    'ArrayAccessible' // 数组或继承自 \ArrayAccess
    'countable' // 数组或实现 \Countable
    'iterable' // 数组或实现 \Traversable
    'instanceOf' // 某个类的实例
    'instanceOfAny' // 一组类中某个类的实例
    'empty' // 空
    'enum' // 枚举型
    'min' // 整型最小值
    'max' // 整型最大值
    'between' // 整型范围
    'required' // 是否必要
    'pattern' // 字符串正则
    'minItems' // 数组最小长度
    'maxItems' // 数组最大长度
```

上面对支持的数据类型进行说明，参数检验底层利用了php组件webmozart，[传送门](https://github.com/webmozart/assert)

有些没有列出的写法，可以直接采用webmozart中的关键字即可

### 1.2 举例

比较常用的场景是对请求方法所接收到的参数进行检验，如下

```php
public class TestController
{
    /**
     * @param int $a {@assert min:1|max:10}
     * @param string $b {@assert optional|min:11|max:50} {@error im error} <-- 检验出错时返回信息
     * @param Object $c
     * @param int $d {@enum ~[1, 2, 3]}
     * @param string $e {@enum ~["abc","dfe","cdm"]}
     *
     * @return string[]
     */
    public function test(int $a, string $b, Object $c, int $d, string $e)
    {
    }

}
```

```php
public class Object
{
    /**
     * @optional
     * @assert min:10|max:50
     * @error property error
     * @var string
     */
    public $str;

    /**
     * @assert min:5|max:100
     * @var string[]
     */
    public $strArr;

    /**
     * @enum ~[1,2,3]
     * @var int
     */
    public $state;
}
```

## 2. 参数检验原理(待补充)











