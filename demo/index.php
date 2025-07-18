<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/bootstrap-doctrine.php';
require_once __DIR__ . '/src/Demo/Product.php';

$debugbarRenderer->setBaseUrl('../vendor/php-debugbar/php-debugbar/src/DebugBar/Resources');

$debugStack = new Doctrine\DBAL\Logging\DebugStack();
$entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
$debugbar->addCollector(new DebugBar\Bridge\Doctrine\DoctrineCollector($debugStack));

$product = new Demo\Product();
$product->setName("foobar");
$product->setUpdated();

$entityManager->persist($product);
$entityManager->flush();
$entityManager->createQuery("select p from  Demo\\Product p where p.updated>:u")->setParameter("u", new \DateTime('1 hour ago'))->execute();
$entityManager->createQuery("select p from  Demo\\Product p where p.name=:c")->setParameter("c", "<script>alert();</script>")->execute();
render_demo_page();
