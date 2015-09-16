<?php

namespace Jehaby\Viomedia;

use PDO;


class DataManager
{

    private $db;


    public function __construct()
    {
        $this->db = new DB();
    }


    public function createFolder($parent_id = NULL, $title = NULL)
    {
        $this->db->beginTransaction();
        $statement = $this->db->prepare("INSERT INTO folders(parent_id, title) VALUES (?, ?)");
        $statement->execute([$parent_id, $title]);
        $statement = $this->db->prepare("INSERT INTO folders(parent_id, title) VALUES (?, ?)");
        $statement->execute([$parent_id, $title]);

        $this->db->commit();
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
