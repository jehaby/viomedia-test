<?php

require 'vendor/autoload.php';

use Jehaby\Viomedia\Migration;
use Jehaby\Viomedia\DataManager;


if (isset($argv[1]) && $argv[1] === 'm') {
    $migration = new Migration();
    $migration->migrate();
    $migration->seed();
}

$dm = new DataManager();


