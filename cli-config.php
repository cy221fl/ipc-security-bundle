<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/tests/AppKernel.php';

$appKernel = new AppKernel('test', true);
$appKernel->boot();
$container = $appKernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();

return ConsoleRunner::createHelperSet($entityManager);
