<?php
// cli-config.php
include __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/demo/bootstrap-doctrine.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::run(
    new \Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider($createEntityManager())
);
