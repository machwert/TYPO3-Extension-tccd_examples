<?php

declare(strict_types=1);

namespace Machwert\TccdExamples\Event;

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

# Extending Existing Functionality [16] - PSR-14 events (Goal 2)

final class ModifyPagetitleEvent
{
    public function __construct(
    ) {}

    public function setTccdExamplesPagetitle($pageTitle) {
        $GLOBALS['TSFE']->indexedDocTitle = $pageTitle;
        $titleProvider = GeneralUtility::makeInstance(\Machwert\TccdExamples\PageTitle\MyRecordTitleProvider::class);
        $titleProvider->setTitle($pageTitle);

        // set og:title
        $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty('og:title');
        $metaTagManager->addProperty('og:title', $pageTitle);
    }
}