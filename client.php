<?php

require 'vendor/autoload.php';

use Jehaby\Viomedia\Migration;
use Jehaby\Viomedia\DataManager;

if ($argc > 1 && in_array('m', $argv)) {
    $migration = new Migration();
    $migration->migrate();
    $migration->seed();
    die();
}

$dm = new DataManager();
//$dm->createFolder(15, 'heheheh');

//print_r($dm->getAllNodes(0));

var_dump($dm->deleteFolder(1));



