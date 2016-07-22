<?php
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\NotFoundException;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Slim\Middleware\JwtAuthentication\RequestPathRule;
use Psr7Middlewares\Middleware\TrailingSlash;

$app = require(__DIR__.'/../app/bootstrap.php');

// true adds the trailing slash (false removes it)
$app->add(new TrailingSlash(false));



require(APPPATH.'routes.php');

$app->run();
