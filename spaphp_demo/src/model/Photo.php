<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/11/5
 * Time: 下午7:44
 */

namespace app\model;


class Photo
{
    /**
     * @var int
     * @assert min:1
     * @assert max:9
     */
    public $id;

    /**
     * @var string
     * @assert minLength:3|maxLength:5
     */
    public $name;

    /**
     * @var Person
     */
    public $owner;
}
