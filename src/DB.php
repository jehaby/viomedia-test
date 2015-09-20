<?php


namespace Jehaby\Viomedia;

use PDO;
use PDOException;
use PDOStatement;


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


    public function prepare($statement, array $driver_options = array())
    {
        if (! $res = parent::prepare($statement, $driver_options)) {
            var_dump($this->errorInfo());
            die();
        }
        return $res;
    }


    public function executeStatement(PDOStatement $statement, $input_parameters = null)
    {
        if (! $statement->execute($input_parameters)) {
            var_dump($statement->errorInfo());
            die();
        }
        return true;
    }


}