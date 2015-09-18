<?php


namespace Jehaby\Viomedia;

use PDO;
use PDOException;


class DB extends PDO
{

    public function __construct() {
        try {
            parent::__construct("sqlite:storage/sqlite.db");
            parent::exec('PRAGMA foreign_keys = ON;');
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function exec($statement, $errorMessage = NULL)
    {
        try {
            $res = parent::exec($statement);
            $this->checkQueryResult($res, $errorMessage);
            return $res;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    private function checkQueryResult($res, $errorMessage)
    {
        if ($res === false) {
            print_r($this->errorInfo());
        } else {
            if ($errorMessage != NULL) {
                echo $errorMessage;
            }
        }
    }


}