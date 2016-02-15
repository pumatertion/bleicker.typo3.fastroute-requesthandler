<?php

namespace Bleicker\TYPO3\FastRoute\RequestHandler;

use Bleicker\FastRoute\RequestHandler\RequestHandler as HttpRequestHandlerImplementation;
use Bleicker\FastRoute\RequestHandler\Routes\Exceptions\NotAllowedException;
use Bleicker\FastRoute\RequestHandler\Routes\Exceptions\NotFoundException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RequestHandler
 *
 * @package Bleicker\TYPO3\FastRoute\RequestHandler\Http
 */
class RequestHandler implements RequestHandlerInterface
{

    /**
     * @var string
     */
    public static $uriPattern;

    /**
     * @var int
     */
    public static $priority = 9999;

    /**
     * Handles a raw request
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handleRequest(ServerRequestInterface $request)
    {
        try {
            return $this->getRequestHandlerImplementation()->handleRequest($request);
        } catch (NotFoundException $exception) {
            $response = new Response();
            return $response->withStatus(404);
        } catch (NotAllowedException $exception) {
            $response = new Response();
            return $response->withStatus(405);
        } catch (Exception $exception) {
            $response = new Response();
            return $response->withStatus(500);
        }
    }

    /**
     * @return HttpRequestHandlerImplementation
     */
    protected static function getRequestHandlerImplementation()
    {
        return GeneralUtility::makeInstance(HttpRequestHandlerImplementation::class);
    }

    /**
     * {@inheritdoc}
     */
    public function canHandleRequest(ServerRequestInterface $request)
    {
        if (static::$uriPattern === null || (string)static::$uriPattern === '') {
            return false;
        }

        return (boolean)preg_match(
            static::$uriPattern,
            $request->getUri()->getPath()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return static::$priority;
    }
}
