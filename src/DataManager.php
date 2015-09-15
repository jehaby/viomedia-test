<?php

namespace Jehaby\Viomedia;

use PDO;


class DataManager
{

    private $db;


    public function __construct()
    {
        $this->db = DB::getInstance();
    }


    public function createFolder($id = 0)
    {



        // TODO: check id in sql!
        $this->checkId($id);
    }


    public function deleteFolder($id)
    {
        $this->checkId($id);
    }


    public function insertNode($id = 0)
    {
        $this->checkId($id);

    }


    public function getAllNodes($id)
    {
        $this->checkId($id);

    }


    private function checkIdAndThrowException($id)
    {
        if (! $this->idIsValid($id) ) {
            throw new Exception;
        }
    }


    private function idIsValid($id)
    {


    }




}
