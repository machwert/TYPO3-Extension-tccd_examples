<?php

namespace Machwert\TccdExamples\EventListener;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\ModifyHrefLangTagsEvent;

# Extending Existing Functionality [16] - PSR-14 events (Goal 2)

/* Servics.yaml:
   Machwert\TccdExamples\EventListener\OwnTccdExamplesEventListener: # Extending Existing Functionality [16] - PSR-14 events (Goal 2)
    tags:
      - name: event.listener
        identifier: 'tccdExamples/testEventListener'
        before: 'typo3-seo/hreflangGenerator'
        event: Machwert\TccdExamples\Event\OwnTccdExamplesEvent

 */
class OwnTccdExamplesEventListener
{
    public function __invoke(): void
    {
        echo("hier");
        die();
    }
}