<?php
    namespace phpcn;
    class Teacher{
        // 构造方法
        public function __construct(
        	public mixed $name,
        	public $school
        ){
            
        }
        public function fun(){
            // 在类中使用伪变量: "$this" 引用当前类的成员变量
            return '姓名：'.$this->name[1].'，学校：'.$this->school.'<hr/>';
        }
    }
    // 实例化
    $obj = new Teacher(['灭绝师太','西门大官人'],'PHP中文网');
    echo $obj->fun();