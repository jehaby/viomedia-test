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
        $fullPath = $this->getFullPath($parentId);

        $statement = $this->db->prepare("INSERT INTO folders(parent_id, title, lvl) VALUES (:parent_id, :title, :lvl)");
        $statement->bindValue(':parent_id', $parentId, PDO::PARAM_INT);
        $statement->bindValue(':title', $title, PDO::PARAM_STR);                                 // TODO: should I check it on NULL here?
        $statement->bindValue(':lvl', $fullPath[count($fullPath) -1]['lvl'] + 1, PDO::PARAM_INT);   // TODO: maybe refactor in class
        $statement->execute();

        $newFolderId = $this->db->lastInsertId();

        $this->db->exec(
            "INSERT INTO all_ancestor_folders(folder_id, ancestor_id) VALUES " .
            implode(
                ', ',
                array_map(
                    function($item) use ($newFolderId) {
                        return "($newFolderId, {$item['folder_id']} )";
                    }, array_slice($fullPath, 0, -1)
                )
            )
        );
    }


    private function getFullPath($folderId)
    {
        $statement = $this->db->prepare('
SELECT aaf.ancestor_id AS folder_id, f.lvl AS lvl
FROM all_ancestor_folders aaf JOIN folders f ON f.id = aaf.ancestor_id
WHERE aaf.folder_id = :folder_id
UNION SELECT parent_id, lvl-1 FROM folders WHERE id = :folder_id AND parent_id NOT NULL
UNION SELECT id, lvl FROM folders WHERE id = :folder_id
ORDER BY lvl;
');

        if ($statement === false) {
            var_dump($this->db->errorInfo());
            die();
        }

        $statement->bindValue(':folder_id', $folderId, PDO::PARAM_INT);
        $this->db->executeStatement($statement);
        return $statement->fetchAll(PDO::FETCH_ASSOC); // TODO: try catch
    }


    public function deleteFolder($folderId)
    {
        $childFolders = implode(',', $this->getAllChildFolders($folderId));

        $this->db->beginTransaction();

        $this->db->exec("DELETE FROM nodes WHERE folder_id IN ($childFolders);");
        $this->db->exec("DELETE FROM all_ancestor_folders WHERE folder_id IN ($childFolders) OR ancestor_id IN ($childFolders);");
        $this->db->exec("DELETE FROM folders WHERE id IN ($childFolders);");

        $this->db->commit();
    }


    private function getAllChildFolders($folderId)
    {
        $statement = $this->db->prepare('
SELECT folder_id, lvl FROM all_ancestor_folders JOIN folders
WHERE ancestor_id = :folder_id
UNION SELECT id, lvl FROM folders WHERE parent_id = :folder_id OR id = :folder_id ORDER BY lvl;
');
        $statement->bindValue(':folder_id', $folderId, PDO::PARAM_INT);
        $this->db->executeStatement($statement);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function insertNode($value, $folderId = null)
    {
        $statement = $this->db->prepare('insert into nodes(val, folder_id) values (:val, :folder_id)');
        $statement->bindValue(':val', $value, PDO::PARAM_STR);
        $statement->bindValue(':folder_id', $folderId, PDO::PARAM_INT);
        $this->db->executeStatement($statement);
    }


    public function getAllNodes($folderId) //
    {

        var_dump($this->getAllChildFolders($folderId));

        $statement = $this->db->prepare('
SELECT * FROM nodes WHERE folder_id IN (
SELECT folder_id from all_ancestor_folders WHERE ancestor_id = :folder_id
UNION SELECT id from folders WHERE parent_id = :folder_id OR id = :folder_id);
        ');
        $statement->bindValue(':folder_id', $folderId, PDO::PARAM_INT);
        $this->db->executeStatement($statement);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function changeParentForFolder($folderId, $newParentId = null)
    {

    }


    public function changeParentForNode($nodeId, $newParentId = null)
    {

    }



}
