<?php

use Goutte\Client;
use ResultParser\AuxParser;

require '../vendor/autoload.php';

// VERBAND/VEREINE
$aux = new AuxParser(__DIR__);
$aux->associations();