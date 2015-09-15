<?php

require 'vendor/autoload.php';

use Jehaby\Viomedia\Migration;


$migration = new Migration();
$migration->migrate();
$migration->seed();