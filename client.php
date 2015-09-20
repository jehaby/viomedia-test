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

print_r($dm->getAllNodes(10));
die();

//$dm->createFolder(15, 'heheheh');


var_dump($dm->deleteFolder(1));



