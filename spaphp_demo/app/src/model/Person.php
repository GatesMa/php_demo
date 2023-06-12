<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/11/14
 * Time: 上午11:01
 */

namespace app\model;


class Person
{

    /**
     * @var string
     * @assert minLength:3|maxLength:8
     */
    public $name;

    /**
     * @var string
     * @enum ~["male", "female"]
     */
    public $sex;
}
