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


    public function createFolder($parentId = NULL, $title = NULL)
    {

        var_dump($this->getFullPath($parentId));
        die();


        $this->db->beginTransaction();
        $statement = $this->db->prepare("INSERT INTO folders(parent_id, title) VALUES (?, ?)");
        $statement->execute([$parentId, $title]);

        $statement = $this->db->prepare("INSERT INTO all_ancestor_folders(folder_id, ancestor_id) VALUES (?, ?)");
        $statement->execute([$parentId, $title]);
        $this->db->commit();
    }

    private function getFullPath($folderId)
    {
        $statement = $this->db->prepare("
SELECT ancestor_id, lvl FROM all_ancestor_folders WHERE folder_id = :folder_id
UNION SELECT parent_id FROM folders WHERE id = :folder_id AND parent_id NOT NULL
");

        if ($statement === false) {
            echo 'wtf';
            var_dump($this->db->errorInfo());
            die();
        }

//        $statement->bindValue(':folder_id', $folderId);
        $statement->execute([':folder_id' => $folderId]);


        return $statement->fetchAll(PDO::FETCH_ASSOC);
        // insert records in all_ancestor_folders table
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
