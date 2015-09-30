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


if ($argc > 1) {

    switch ($argv[1]) {
        case 'ln': // list nodes
            $dm->getAllNodes(isset($argv[2]) ? $argv[2] : null);
            break;

        case 'lf': // list folders

            break;

        case 'dn': //

            break;

        case 'df':

            break;

        case 'in':

            break;

        case 'if':

            break;

        default:

            break;

    }


}

$dm->createFolder(0);
die();

$dm->getAllNodes(666);
die();

//$dm->createFolder(15, 'heheheh');


var_dump($dm->deleteFolder(1));



