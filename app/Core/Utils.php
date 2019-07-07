<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\Core;
/**
 * Description of Constantes
 *
 * @author sapc_
 */
class Utils {

    //put your code here


    public static function baseUrl() {
        //establecer la ruta glopal base del proyecto
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $baseDir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        $baseDir = str_replace('public/', '', $baseDir);
        $host = array_key_exists('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : gethostbyaddr($_SERVER["REMOTE_ADDR"]);
        return $protocol . $host . $baseDir;
    }

}
