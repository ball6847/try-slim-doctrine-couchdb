<?php
namespace Library\Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;

final class Error extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        // Log the message
        $this->logger->critical((string) $exception);

        return $response
            ->withStatus(500)
            ->withHeader('Content-Type', 'text/json')
            ->write(json_encode(['error' => 'Something went wrong!']));
            // ->write(json_encode(['error' => $exception->getMessage()]));

        return parent::__invoke($request, $response, $exception);
    }
}
