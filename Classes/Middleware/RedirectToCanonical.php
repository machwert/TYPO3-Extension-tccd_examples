<?php

namespace Machwert\TccdExamples\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;

class RedirectToCanonical implements MiddlewareInterface {
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        //die('redirecttocanonical middleware');
        /*
        if (true) {
            return GeneralUtility::makeInstance(ErrorController::class)
                ->unavailableAction(
                    $request,
                    'This page is temporarily unavailable.'
                );
        }
        */

        return $handler->handle($request);
    }
}
