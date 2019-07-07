<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Runtime;

use \Twig_Loader_Filesystem;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class RenderPages {

    protected $templateEngine;

    public function __construct() {
        $loader = new Twig_Loader_Filesystem('../resources/views');
        $this->templateEngine = new \Twig_Environment($loader, array(
            'debug' => true,
            'cache' => false,
        ));

        $this->templateEngine->addFilter(new \Twig_SimpleFilter('baseUrl', function ($path) {
            return BASE_URL . $path;
        }));
    }

    public function renderHTML($fileName, $data = []) {
        return new HtmlResponse($this->templateEngine->render($fileName, $data));
    }

    public function renderJson($data = []) {
        return new JsonResponse($data);
    }

}
