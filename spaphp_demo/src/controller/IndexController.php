<?php

namespace app\controller;

/**
 * Class IndexController
 *
     * @package app\controller
     * @api
     */
class IndexController extends Controller
{
    /**
     * @get /
     *
     * @return string
     */
    public function index()
    {
        return "Welcome, enjoy your life！<a href=\"/docs/\">swagger 文档</a>";
    }
}
