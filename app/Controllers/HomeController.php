<?php

namespace App\Controllers;

use App\Runtime\RenderPages;

class HomeController extends RenderPages {

    public function getHome() {

        return $this->renderHTML('home.twig');
    }
    
    public function getApartamentos() {

        return $this->renderHTML('home.twig');
    }
    
    public function getVisitas() {

        return $this->renderHTML('home.twig');
    }

}
