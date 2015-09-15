<?php


namespace Jehaby\Viomedia;

use PDO;
use PDOException;


class DB
{

    private static $db;


    private function __construct() {

        if (is_null(self::$db)) {
            try {
                self::$db = new PDO("sqlite:storage/sqlite.db");
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }


    }


    private function __clone() {}


    public static function getInstance()
    {


        return self::$db;
    }

}