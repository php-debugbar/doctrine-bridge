<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;

$isDevMode = true;
// Create a simple "default" Doctrine ORM configuration for Annotations
$config = new Configuration();
$config->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/src/Demo']));
$config->setProxyDir(__DIR__ . '/proxies');
$config->setProxyNamespace('App\Proxies');
$config->setAutoGenerateProxyClasses($isDevMode); // Dev mode, sin caché
// or with symfony cache
//$config = ORMSetup::createAttributeMetadataConfiguration(array(__DIR__."/src/Demo"), $isDevMode);
// or if you prefer yaml or XML
//$config = ORMSetup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = ORMSetup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// database configuration parameters
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/db.sqlite',
);

// obtaining the entity manager
$createEntityManager = function () use (&$config, $conn){
    if (method_exists(EntityManager::class, 'create')) {
        return EntityManager::create($conn, $config);
    } else {
        return new EntityManager(DriverManager::getConnection($conn, $config), $config);
    }
};
