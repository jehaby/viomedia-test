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


    public function createFolder($parentId = NULL, $title = NULL)  // TODO: if parentId doesn't exist, should return false
    {
        $fullPath = $this->getFullPath($parentId);
        $lvl = empty($fullPath) ? 0 : $fullPath[count($fullPath) -1]['lvl'] + 1;

        $this->db->beginTransaction();

        $statement = $this->db->prepare("INSERT INTO folders(parent_id, title, lvl) VALUES (:parent_id, :title, :lvl)");
        $this->db->bindValue($statement, ':parent_id', $parentId, PDO::PARAM_INT);
        $this->db->bindValue($statement, ':title', $title, PDO::PARAM_STR);
        $this->db->bindValue($statement, ':lvl', $lvl, PDO::PARAM_INT);
        $res = $statement->execute();

        if ($lvl > 1) {

            $newFolderId = $this->db->lastInsertId();

            $this->db->exec(
                "INSERT INTO all_ancestor_folders(folder_id, ancestor_id) VALUES " .
                $this->createAllAncestorFoldersValuesString($newFolderId, $fullPath)
            );
        }

        return $this->db->commit();
    }


    public function getFullPath($folderId)
    {
        if (is_null($folderId))
            return [];

        $statement = $this->db->prepare('
SELECT aaf.ancestor_id AS folder_id, f.lvl AS lvl
FROM all_ancestor_folders aaf JOIN folders f ON f.id = aaf.ancestor_id
WHERE aaf.folder_id = :folder_id
UNION SELECT parent_id, lvl-1 FROM folders WHERE id = :folder_id AND parent_id NOT NULL
UNION SELECT id, lvl FROM folders WHERE id = :folder_id
ORDER BY lvl;
');

        $statement->bindValue(':folder_id', $folderId, PDO::PARAM_INT);
        $this->db->executeStatement($statement);

        $res = $statement->fetchAll(PDO::FETCH_ASSOC); // TODO: try catch?

        if (empty($res)) {
            throw new \LogicException('There is no folder with such id');
        }

        return $res;
    }


    /**
     * @param $newFolderId
     * @param $fullPath
     * @return string
     */
    private function createAllAncestorFoldersValuesString($newFolderId, $fullPath)
    {
        return implode(
            ', ',
            array_map(
                function ($item) use ($newFolderId) {
                    return "($newFolderId, {$item['folder_id']} )";
                }, array_slice($fullPath, 0, -1)
            )
        );
    }


    public function deleteFolder($folderId)
    {
        $childFolders = implode(',', $this->getAllChildFolders($folderId, true));

        $this->db->beginTransaction();

        $this->db->exec("DELETE FROM nodes WHERE folder_id IN ($childFolders);");
        $this->db->exec("DELETE FROM all_ancestor_folders WHERE folder_id IN ($childFolders) OR ancestor_id IN ($childFolders);");
        $this->db->exec("DELETE FROM folders WHERE id IN ($childFolders);");

        return $this->db->commit();
    }


    private function getAllChildFolders($folderId, $dontGroup = false)
    {
        $statement = $this->db->prepare('
SELECT f.lvl, aaf.folder_id FROM all_ancestor_folders aaf JOIN folders f ON aaf.folder_id = f.id
WHERE aaf.ancestor_id = :folder_id
UNION SELECT lvl, f.id FROM folders f WHERE parent_id = :folder_id OR id = :folder_id ORDER BY lvl;
');

        $statement->bindValue(':folder_id', $folderId, PDO::PARAM_INT);
        $this->db->executeStatement($statement);

        if ($dontGroup) {
            return $statement->fetchAll(PDO::FETCH_COLUMN, 1);
        }

        return $statement->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_NUM);
    }


    public function insertNode($value, $folderId = null)
    {
        $statement = $this->db->prepare('insert into nodes(val, folder_id) values (:val, :folder_id)');
        $this->db->bindValue($statement, ':val', $value);
        $this->db->bindValue($statement, ':folder_id', $folderId, PDO::PARAM_INT);
        return $this->db->executeStatement($statement);
    }


    public function getAllNodes($folderId) //
    {

        print_r($childFolders = $this->getAllChildFolders($folderId));

        $statement = $this->db->prepare('
SELECT * FROM nodes WHERE folder_id IN (
SELECT folder_id from all_ancestor_folders WHERE ancestor_id = :folder_id
UNION SELECT id from folders WHERE parent_id = :folder_id OR id = :folder_id);
        ');
        $statement->bindValue(':folder_id', $folderId, PDO::PARAM_INT);
        $this->db->executeStatement($statement);
        $nodes = $statement->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);

        print_r($nodes);

        return $nodes;
    }



    public function changeParentForFolder($folderId, $newParentId = null)
    {
        // change records in all_ancestor_folders,
        //
        // folders, nodes

        $this->db->beginTransaction();



        $this->db->commit();
    }


    public function changeParentForNode($nodeId, $newParentId = null)
    {
        $statement = $this->db->prepare("UPDATE nodes SET folder_id = :folder_id WHERE id = :node_id");
        $statement->bindValue(':folder_id', $newParentId);
        $statement->bindValue(':node_id', $nodeId);
        $this->db->executeStatement($statement);
    }




}
