<?php

namespace Bleicker\TYPO3\FastRoute\RequestHandler\Tests\Unit;

use Bleicker\FastRoute\RequestHandler\Routes\Routes;
use Bleicker\TYPO3\FastRoute\RequestHandler\RequestHandler;
use Bleicker\TYPO3\FastRoute\RequestHandler\Tests\Unit\Fixtures\Controller\RoutableController;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class RequestHandlerTest
 *
 * @package Bleicker\TYPO3\FastRoute\RequestHandler\Tests\Unit
 */
class RequestHandlerTest extends UnitTestCase
{

    /**
     * @test
     */
    public function canHandleTest()
    {
        $request = new ServerRequest('http://www.google.de/api/foo/bar/baz', 'get');
        $requestHandler = new RequestHandler();

        /** @var RequestHandler $requestHandler */
        $this->assertTrue($requestHandler->canHandleRequest($request));
    }

    /**
     * @test
     */
    public function canNotHandleTest()
    {
        $request = new ServerRequest('http://www.google.de/typo3/foo/bar/baz', 'get');
        $requestHandler = new RequestHandler();

        /** @var RequestHandler $requestHandler */
        $this->assertFalse($requestHandler->canHandleRequest($request));
    }

    /**
     * @test
     */
    public function canNotHandleTestIfUriPatternNullTest()
    {
        RequestHandler::$uriPattern = null;
        $request = new ServerRequest('http://www.google.de/typo3/foo/bar/baz', 'get');
        $requestHandler = new RequestHandler();

        /** @var RequestHandler $requestHandler */
        $this->assertFalse($requestHandler->canHandleRequest($request));
    }

    /**
     * @test
     */
    public function noMatchingRouteFoundForUriTest()
    {
        Routes::add('get', '/api/{foo}/bar/{baz}', RoutableController::class, 'routableAction');
        $request = new ServerRequest('http://www.google.de/typo3/', 'get');
        $requestHandler = new RequestHandler();

        /** @var RequestHandler $requestHandler */
        $response = $requestHandler->handleRequest($request);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function noMatchingRouteFoundForMethodTest()
    {
        Routes::add('get', '/api/{foo}/bar/{baz}', RoutableController::class, 'routableAction');
        $request = new ServerRequest('http://www.google.de/api/foo/bar/baz', 'post');
        $requestHandler = new RequestHandler();

        /** @var RequestHandler $requestHandler */
        $response = $requestHandler->handleRequest($request);
        $this->assertEquals(405, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function handleTest()
    {
        Routes::add('get', '/api/{foo}/bar/{baz}', RoutableController::class, 'routableAction');
        $request = new ServerRequest('http://www.google.de/api/foo/bar/baz', 'get');
        $requestHandler = new RequestHandler();

        /** @var RequestHandler $requestHandler */
        $response = $requestHandler->handleRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function exceptionTest()
    {
        Routes::add('get', '/api/{foo}/bar/{baz}', RoutableController::class, 'exceptionAction');
        $request = new ServerRequest('http://www.google.de/api/foo/bar/baz', 'get');
        $requestHandler = new RequestHandler();

        /** @var RequestHandler $requestHandler */
        $response = $requestHandler->handleRequest($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function priorityTest()
    {
        $requestHandler = new RequestHandler();
        $this->assertEquals(RequestHandler::$priority, $requestHandler->getPriority());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        RequestHandler::$uriPattern = ('|^/api/.*|');
        parent::setUp();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        RequestHandler::$uriPattern = null;
        Routes::$routeRegister = [];
        parent::tearDown();
    }
}
