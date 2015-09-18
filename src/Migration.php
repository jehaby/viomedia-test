<?php


namespace Jehaby\Viomedia;


use PDO;


class Migration {

    
    private $db;


    public function __construct()
    {
        $this->db = new DB();
    }


    public function migrate()
    {
        $sql =  <<<SQL
DROP TABLE IF EXISTS nodes;
DROP TABLE IF EXISTS all_ancestor_folders;
DROP TABLE IF EXISTS folders;
CREATE TABLE folders (id INTEGER PRIMARY KEY, title TEXT, parent_id INTEGER REFERENCES folders(id));
CREATE TABLE nodes (id INTEGER PRIMARY KEY, val TEXT, folder_id INTEGER REFERENCES folders(id));
CREATE TABLE all_ancestor_folders (
     folder_id INTEGER REFERENCES folders(id),
     ancestor_id INTEGER REFERENCES folders(id),
     lvl INTEGER,
     PRIMARY KEY (folder_id, ancestor_id)
);
SQL;

        if ($this->db->exec($sql) === false) {
            var_dump($this->db->errorInfo());
        } else {
            echo "Tables have been created.\n";
        }

    }

    
    public function seed()
    {
        $sql =  <<<SQL
INSERT INTO folders(id,parent_id) VALUES (0, null), (1, 0), (5, 0), (10, 1), (13, 10), (15, 10);
INSERT INTO nodes(val, folder_id) VALUES ('bob', 0), ('joe', 0), ('steve', 1), ('alice', 10), ('mark', 10), ('donald', 13), ('fred', 13), ('kevin', 15);
INSERT INTO all_ancestor_folders(folder_id, ancestor_id, lvl) VALUES (13, 1, 3), (15, 1, 3);
SQL;
        if ($this->db->exec($sql) === false) {
            var_dump($this->db->errorInfo());
        } else {
            echo "Tables have been seeded.\n";
        }

    }


}