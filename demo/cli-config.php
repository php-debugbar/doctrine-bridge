<?php
// cli-config.php
include __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/bootstrap-doctrine.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;

return ConsoleRunner::createHelperSet($entityManager);
