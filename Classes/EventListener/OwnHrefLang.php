<?php

namespace Machwert\TccdExamples\EventListener;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Event\ModifyHrefLangTagsEvent;

# Extending Existing Functionality [16] - PSR-14 events (Goal 2)

class OwnHrefLang
{
    public function __invoke(ModifyHrefLangTagsEvent $event): void
    {
        $hrefLangs = $event->getHrefLangs();
        $request = $event->getRequest();
        $hrefLangModified = false;

        $queryParams = ($request->getQueryParams());
        $tccdUid = $queryParams['tx_tccdexamples_tccdpluginshow']['tccd'] ?? 0;
        $tccdUid = (int)$tccdUid;

        if ($tccdUid > 0) {
            $siteLanguages = $request->getAttribute('site')->getLanguages();
            foreach($siteLanguages as $key => $val) {
                if ($val->getLanguageId() > 0) {
                    // check if translation exists
                    // SELECT uid FROM tx_tccdexamples_domain_model_tccd WHERE l10n_parent = $tccdUid AND sys_language_uid = $val->getLanguageId()

                    $notfound = true;
                    $tableName = 'tx_tccdexamples_domain_model_tccd';
                    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName)->createQueryBuilder();

                    $queryBuilder
                        ->select('uid')
                        ->from($tableName)
                        ->where(
                            $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter($tccdUid, Connection::PARAM_INT)),
                            $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($val->getLanguageId(), Connection::PARAM_INT)),
                        );

                    $result = $queryBuilder->executeQuery();

                    while ($row = $result->fetchAssociative()) {
                        $notfound = false;
                    }

                    if($notfound) {
                        unset($hrefLangs[$val->getHreflang()]);
                        $hrefLangModified = true;
                    }
                }
            }
            if ($hrefLangModified) {
                $event->setHrefLangs($hrefLangs);
            }
        }
    }
}