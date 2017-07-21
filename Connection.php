<?php

/**
 * Created by PhpStorm.
 * User: Jean-Mathieu
 * Date: 3/1/2016
 * Time: 7:55 PM
 */
class Connection
{
    private $connection = null;
    public function getConnection(){
        try{
         return new PDO("mysql:host=localhost;dbname=", '', '');
        }catch(Exception $e){
            echo "Can't connect to DATABASE!";
            return null;
        }
    }
}