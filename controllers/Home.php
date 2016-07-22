<?php
namespace Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Library\Handler\Json;

class Home extends Base
{
    public function index(Request $request, Response $response)
    {
        $article = new \Entity\Article;
        $article->setTitle('Hello World');
        $article->setContent('Hello World from Moon');

        $this->doctrine->persist($article);
        $this->doctrine->flush();

        return $this->jsonHandler($request, $response, ['message' => 'welcome']);
    }
}
