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

$dm->insertNode('sdjlfksjdf', 666);

return;

//$dm->getAllNodes(13);
//var_dump($dm->getAllNodes(13));
print_r($dm->getAllNodes(13));
return;

$reflector = new \ReflectionClass('Jehaby\Viomedia\DataManager'); // TODO: try without namespace
$method = $reflector->getMethod('getFolderIdWithChildren');
$method->setAccessible(true);

var_dump($method->invokeArgs($dm, [666]));
return;



$dm->deleteFolder(0);

$dm->createFolder(666);
die();

$dm->getAllNodes(666);
die();

//$dm->createFolder(15, 'heheheh');


var_dump($dm->deleteFolder(1));



