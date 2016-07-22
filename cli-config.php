<?php
/**
 *  Doctrine Console Runner
 *
 *  See available commands by running: ./vendor/bin/doctrine
 */
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\DialogHelper;

// --------------------------------------------------------------------
// load Slim application, we need $doctrine DoctrineMiddleware

$app = require(__DIR__."/app/bootstrap.php");
$doctrine = $app->getContainer()['doctrine'];

// --------------------------------------------------------------------
// create doctrine console application

$helperSets = new HelperSet(array(
    'db' => new ConnectionHelper($doctrine->getConnection()),
    'em' => new EntityManagerHelper($doctrine),
    'dialog' => new DialogHelper
));

ConsoleRunner::run($helperSets, [
    new Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand,
    new Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand,
    new Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand,
    new Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand,
    new Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand,
    new Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand,
]);
