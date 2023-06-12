<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/11/12
 * Time: 下午2:35
 */

namespace app\controller;

use app\model\Photo;


/**
 * Class PhotoController
 *
 * REST 风格示例
 *
 * @package app\controller
 *
 * @api
 */
class PhotoController extends Controller
{

    /**
     * @get /photo
     *
     * @return string
     */
    public function index()
    {

    }

    /**
     * @get /photo/create
     *
     * @return bool
     */
    public function create()
    {

    }

    /**
     * @post /photo
     *
     * @param Photo $photo
     *
     * @return int
     */
    public function store(Photo $photo)
    {

    }

    /**
     * @get /photo/{id}
     *
     * @param int $id
     *
     * @return Photo
     */
    public function show($id)
    {

    }

    /**
     * @get /photo/{id}/edit
     *
     * @param int $id
     *
     * @return Photo
     */
    public function edit($id)
    {

    }

    /**
     * @put /photo/{id}
     * @param int $id
     * @param Photo $photo
     * @return bool
     */
    public function update($id, Photo $photo)
    {

    }

    /**
     * @delete /photo/{id}
     * @param int $id
     *
     * @return bool
     */
    public function destroy(int $id)
    {

    }
}
