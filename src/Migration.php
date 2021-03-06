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

CREATE TABLE folders (id INTEGER PRIMARY KEY, title TEXT, parent_id INTEGER REFERENCES folders(id), lvl INTEGER NOT NULL);

CREATE TABLE nodes (id INTEGER PRIMARY KEY, val TEXT, folder_id INTEGER REFERENCES folders(id));

CREATE TABLE all_ancestor_folders (
     folder_id INTEGER REFERENCES folders(id),
     ancestor_id INTEGER REFERENCES folders(id),
     PRIMARY KEY (folder_id, ancestor_id)
);

CREATE TABLE users(id INTEGER PRIMARY KEY, storage TEXT);
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
INSERT INTO folders(id,parent_id,lvl) VALUES (0, null, 0), (1, 0, 1), (5, 0, 1), (10, 1, 2), (13, 10, 3), (15, 10, 3);
INSERT INTO nodes(val, folder_id) VALUES ('bob', 0), ('joe', 0), ('steve', 1), ('alice', 10), ('mark', 10), ('donald', 13), ('fred', 13), ('kevin', 15);
INSERT INTO all_ancestor_folders(folder_id, ancestor_id) VALUES (10, 0), (13, 1), (15, 1), (13, 0), (15, 0);
INSERT INTO users (id, storage) values (42, 'a:3:{s:4:"home";a:3:{s:4:"city";s:5:"Miami";s:5:"state";s:2:"FL";s:8:"location";a:2:{s:3:"lan";d:40;s:4:"long";d:50;}}s:4:"work";a:2:{s:4:"main";a:2:{s:4:"role";s:5:"cheif";s:7:"address";s:9:"Miami, FL";}s:5:"hobby";a:2:{s:4:"role";s:7:"painter";s:7:"address";N;}}s:3:"age";i:68;}');
SQL;

        if ($this->db->exec($sql) === false) {
            var_dump($this->db->errorInfo());
        } else {
            echo "Tables have been seeded.\n";
        }

    }


}