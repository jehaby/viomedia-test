<?php


namespace Jehaby\Viomedia;

use PDO;
use PDOException;
use PDOStatement;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;



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

        $this->logger = new Logger('the_logger', [ new StreamHandler(__DIR__ . 'viomedia-test.log') ] );
    }


    public function exec($statement, $errorMessage = NULL)
    {
        try {
            $res = parent::exec($statement);
            $this->checkQueryResult($res, $errorMessage);
            return $res;
        } catch (PDOException $e) {
            $this->logger->addError("Error in DB::" . __METHOD__ . ': '.  $e->getMessage());
        }
    }

    private function checkQueryResult($res, $errorMessage)
    {
        if ($res === false) {
            $this->logger->addError("Error in DB::" . __METHOD__ . ': '.  json_encode($this->errorInfo()));
        } elseif ($errorMessage != NULL) {
            $this->logger->addError("Error in DB::" . __METHOD__ . ': '.  $errorMessage);
        }
    }


    public function prepare($statement, array $driver_options = array())
    {
        if (! $res = parent::prepare($statement, $driver_options)) {
            $this->logger->addError("Error in DB::prepare: " . $this->errorInfo());
        }
        return $res;
    }


    public function executeStatement(PDOStatement $statement, $inputParameters = null)
    {
        if (! $res = $statement->execute($inputParameters)) {
            $errorInfo = json_encode($statement->errorInfo());
            $queryString = $statement->queryString;
            $this->logger->addWarning(
<<<MSG
            Something wrong in DB::executeStatement. ${errorInfo}
            queryString: $queryString
            inputParameters: $inputParameters
MSG
            );
        }
        return $res;
    }

    public function bindValue(PDOStatement $statement, $parameter, $value, $data_type = PDO::PARAM_STR)
    {
        try {
            if (! $res = $statement->bindValue($parameter, $value, $data_type) ) {
                $this->logger->addWarning("Error in DB::bindValue: couldn't bind value." .
                    " | queryString: " . $statement->queryString .
                    " | parameter: " . $parameter .
                    " | value: " . $value
                );
            }
            return $res;
        } catch (PDOException $e) {
            $this->logger->addError("Error in DB::bindValue: " . $e->getMessage());
        }
    }


}