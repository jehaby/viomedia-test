<?php

namespace Jehaby\Viomedia;

use PDO;



class Migration {

    
    private $db;


    public function __construct() {
        $this->db = DB::getInstance();
    }


    public function migrate() {

        $sql =  <<<SQL

DROP TABLE IF EXISTS folders;
DROP TABLE IF EXISTS nodes;
DROP TABLE IF EXISTS all_ancestor_folders;
CREATE TABLE folders (id INTEGER PRIMARY KEY, title TEXT, parent_id INTEGER REFERENCES folders(id));
CREATE TABLE nodes (id INTEGER PRIMARY KEY, value TEXT, folder_id INTEGER REFERENCES folders(id));
CREATE TABLE all_ancestor_folders (folder_id INTEGER, ancestor_id INTEGER, PRIMARY KEY (folder_id, ancestor_id));

SQL;

        $this->executeSql($sql);

    }

    
    public function seed() {

        $sql =  <<<SQL

INSERT INTO folders(id,parent_id) VALUES (0, null), (1, 0), (5, 0), (10, 1), (13, 10), (15, 10);
INSERT INTO data(value, folder_id) VALUES (bob, 0), (joe, 0), (steve, 1), (alice, 10), (mark, 10), (donald, 13), (fred, 13), (kevin, 15);

SQL;
        $this->executeSql($sql);
        
    }




}