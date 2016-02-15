<?php

namespace Bleicker\TYPO3\FastRoute\RequestHandler\Tests\Unit\Fixtures\Controller;

use Bleicker\Application\ContextInterface;
use Bleicker\FastRoute\RequestHandler\Controller\ControllerInterface;
use Exception;
use Psr\Http\Message\RequestInterface;
use TYPO3\CMS\Core\Http\Response;

/**
 * Class RoutableController
 *
 * @package Bleicker\TYPO3\FastRoute\RequestHandler\Tests\Unit\Fixtures\Controller
 */
class RoutableController implements ControllerInterface
{

    /**
     * @param RequestInterface  $request
     * @param                   $methodName
     * @param array             $methodArguments
     *
     * @return Response
     */
    public function processRequest($methodName, $methodArguments, RequestInterface $request)
    {
        return call_user_func_array(array($this, $methodName), $methodArguments);
    }

    /**
     * @return Response
     */
    public function routableAction()
    {
        return new Response();
    }

    public function exceptionAction()
    {
        throw new Exception('foo');
    }
}
