<?php

declare(strict_types=1);

namespace Machwert\TccdExamples\Domain\Repository;


use Machwert\TccdExamples\Utility\SlugUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * This file is part of the "TCCD examples" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Volker Golbig <typo3@machwert.de>, machwert
 */

/**
 * The repository for Tccds
 */
class TccdRepository extends \Machwert\TccdExamples\Domain\Repository\AbstractRepository
{

    /**
     * @var array
     */
    protected $defaultOrderings = ['sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING];

    /**
     * action findByCategory
     *
     * @param array $categoryArray
     * @return object
     */
    public function findByCategory($categoryArray): QueryResultInterface|array
    {
        // Doctrine DBAL [3]
        $query = $this->createQuery();
        $query->matching($query->contains('categories', $categoryArray));
        //$query->setOrderings(array('title' => \TYPO3\CMS\Extbase\Persistence\Generic\Query::ORDER_ASCENDING));
        return $query->execute();
    }

    /**
     * action findByParameter
     * @param array $filterParameter
     * @return object
     */

    public function findByParameter($filterParameter): QueryResultInterface|array
    {

        // \TYPO3\CMS\Core\Utility\DebugUtility::debug($filterParameter, 'filterParameter');
        $query = $this->createQuery();
        $constraints = [];

        if (isset($filterParameter['category']) && $filterParameter['category']) {
            $categoryArray[] = $filterParameter['category'];
            $constraints[] = $query->contains('categories', $categoryArray);
        }

        if (isset($filterParameter['sword']) && $filterParameter['sword']) {
            $constraints[] = $query->like('title', "%".$filterParameter['sword']."%"); // Queries in the query language of Extbase are automatically escaped.
        }

        if(count($constraints) > 0) {
            $query->matching(
                $query->logicalAnd(...array_values($constraints))
            );
        }

        // Using Query Restrictions [9]
        if ((isset($filterParameter['showHidden']) && $filterParameter['showHidden']) OR
            (isset($filterParameter['showDeleted']) && $filterParameter['showDeleted'])) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }
        if (isset($filterParameter['showDeleted']) && $filterParameter['showDeleted']) {
            $query->getQuerySettings()->setIncludeDeleted(true);
        }
        if (isset($filterParameter['ignoreStorage']) && $filterParameter['ignoreStorage']) {
            $query->getQuerySettings()->setRespectStoragePage(false);
        }

        //$query->setOrderings(array('sorting' => \TYPO3\CMS\Extbase\Persistence\Generic\Query::ORDER_ASCENDING));
        return $query->execute();
    }

    /**
     * action findAllCategories
     *
     * @param array $categoryArray
     * @return object
     */
    public function findAllCategories($sysLanguageUid): QueryResultInterface|array
    {
        // Doctrine DBAL [3]
        // Using the QueryBuilder [8]
        $table = 'sys_category';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table)->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->where($queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid, Connection::PARAM_INT)))
            ->from($table);

        $result = $queryBuilder->executeQuery();

        while ($row = $result->fetchAssociative()) {
            if($sysLanguageUid > 0) {
                $row['uid'] = $row['l10n_parent'];
            }
            $allRows[$row['uid']] = $row;
        }

        return $allRows;
    }


    /*
     *
     *
     * TCCD - Module functions start
     *
     *
     */

    /**
     * action findTranslated
     * @return object
     */

    public function findTranslated($sysLanguageUid): QueryResultInterface|array
    {

        // \TYPO3\CMS\Core\Utility\DebugUtility::debug($filterParameter, 'filterParameter');
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->matching($query->equals('sys_language_uid', $sysLanguageUid));

        //$this->debugQuery($query);
        //$query->setOrderings(array('sorting' => \TYPO3\CMS\Extbase\Persistence\Generic\Query::ORDER_ASCENDING));
        return $query->execute();
    }

    /**
     * action findAllDontRespectStoragePage
     * @return object
     */

    public function findAllDontRespectStoragePage(): QueryResultInterface|array
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        return $query->execute();
    }

    /**
     * action findAllLanguages
     *
     * @return object
     */
    public function findAllLanguages(): QueryResultInterface|array
    {
        // Doctrine DBAL [3]
        // Using the QueryBuilder [8]
        $table = 'sys_language';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table)->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from($table);

        $result = $queryBuilder->executeQuery();

        while ($row = $result->fetchAssociative()) {
            $allRows[$row['uid']] = $row;
        }

        return $allRows;
    }

    /**
     * action translateDatasets
     *
     * @param array $datasets
     * @param int $sysLanguageUid
     * @return array
     */
    public function translateDatasets($datasets, $sysLanguageUid): array
    {
        $backendConfigurationManager = GeneralUtility::makeInstance(BackendConfigurationManager::class);
        $typoscript = $backendConfigurationManager->getTypoScriptSetup();
        $translateLimit = (int) $typoscript['module.']['tx_tccdexamples_web_tccdexamplestccdmodule.']['settings.']['translateLimit'];

        $languageIsoode = '';
        $datasetsAlreadyTranslated = 0;
        $table = 'sys_language';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table)->createQueryBuilder();

        $queryBuilder
            ->select('language_isocode')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($sysLanguageUid, Connection::PARAM_INT))
            )
        ;

        $result = $queryBuilder->executeQuery();
        while ($row = $result->fetchAssociative()) {
            $languageIsoode = $row['language_isocode'];
        }
        if ($languageIsoode != '') {
            foreach($datasets as $key => $dataset) {

                $newTccd = $dataset;
                $newTccd->setL10nParent($dataset->getUid());
                $newTccd->setSysLanguageUid($sysLanguageUid);
                $newTccd->setTitle($this->translateText($dataset->getTitle(), $languageIsoode));
                $newTccd->setDescription($this->translateText($dataset->getDescription(), $languageIsoode));
                $newTccd->setSyllabusdescription($this->translateText($dataset->getSyllabusdescription(), $languageIsoode));

                $returnArray['translatedDatasets'][$dataset->getUid()] = $dataset->getTitle();

                $this->addTranslatedDataset($newTccd);
                $datasetsAlreadyTranslated++;
                if($datasetsAlreadyTranslated >= $translateLimit) {
                    break;
                }
            }
        }
        $returnArray['translatedCount'] = $datasetsAlreadyTranslated;
        return $returnArray;
    }

    public function translateText($text, $languageIsocode) {

        if ($text != null && $text != '') {
            // Regulärer Ausdruck zum Identifizieren von Zeichenfolgen ohne Leerzeichen, die auf bestimmte Dateiendungen enden
            $pattern = '/(?<=\s|^)(\/[^\/\s]+\.(php|html|pdf|jpg|xml|gif|jpeg|png|zip))(?=\s|$)/';

            // Ersetzen von passenden Zeichenfolgen durch Platzhalter
            $text = preg_replace_callback($pattern, function ($matches)
            {
                return str_repeat('*', strlen($matches[0]));
            }, $text);

            // Aufteilen des Textes in Teile zwischen Tags und innerhalb von <pre> Tags
            $parts = preg_split('/(<pre>.*?<\/pre>|\n)/s', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

            // Übersetzen von Textteilen außerhalb von <pre> Tags
            foreach ($parts as &$part)
            {
                if (strpos($part, '<pre>') === false && strpos($part, '</pre>') === false)
                {
                    $part = $this->translateTextWithGoogleTranslator($part, $languageIsocode);
                }
            }

            // Zusammenführen der Teile und Ausgabe des übersetzten Textes
            $text = implode('', $parts);
        }

        return $text;
    }

    public function translateTextWithGoogleTranslator($text, $languageIsocode)
    {
        $backendConfigurationManager = GeneralUtility::makeInstance(BackendConfigurationManager::class);
        $typoscript = $backendConfigurationManager->getTypoScriptSetup();
        $googleTranslatorApiKey = $typoscript['module.']['tx_tccdexamples_web_tccdexamplestccdmodule.']['settings.']['googleTranslatorApiKey'];

        if($googleTranslatorApiKey != '') {
            $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $googleTranslatorApiKey;

            // Text in das JSON-Format konvertieren
            $data = array(
                'q' => $text,
                'target' => $languageIsocode
            );
            $data = json_encode($data);

            // cURL-Optionen für die POST-Anfrage
            $options = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data
            );

            // cURL-Initialisierung
            $curl = curl_init();
            curl_setopt_array($curl, $options);

            // Anfrage an die API senden
            $response = curl_exec($curl);

            // cURL-Verbindung schließen
            curl_close($curl);

            // API-Antwort decodieren und übersetzten Text extrahieren
            $text = json_decode($response, true)['data']['translations'][0]['translatedText'];
        }

        return $text;
    }

    public function addTranslatedDataset($dataset) {

        $tableName = 'tx_tccdexamples_domain_model_tccd';
        $insertArray = [];

        $mappginArray = [
            'pid' => 'pid',
            'title' => 'title',
            'description' => 'description',
            'syllabusdescription' => 'syllabusdescription',
            'version' => 'version',
            'link' => 'link',
            'sysLanguageUid' => 'sys_language_uid',
            'l10nParent' => 'l10n_parent'
        ];

        $properties = $dataset->getAllProperties();

        foreach ($properties as $propertyName => $propertyValue) {
            if(array_key_exists($propertyName, $mappginArray)) {
                $insertArray[$mappginArray[$propertyName]] = $propertyValue;
            }
        }

        $insertArray['crdate'] = time();
        $insertArray['tstamp'] = time();

        $databaseData = $this->getDatabaseData($tableName, $dataset->getUid());

        $insertArray['cruser_id'] = $databaseData['cruser_id'];
        $insertArray['categories'] = $databaseData['categories'];
        $insertArray['images'] = $databaseData['images'];
        $insertArray['sorting'] = (int)$databaseData['sorting'] + 1;

        $lastInsertId = $this->insertTranslatedDataset($tableName, $insertArray);

        $this->copyCategoryConnectionsForTranslation($tableName, $dataset->getUid(), $lastInsertId);
    }

    public function getDatabaseData($tableName, $uid) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName)->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT)));

        $result = $queryBuilder->executeQuery();

        while ($row = $result->fetchAssociative()) {
            $allRows = $row;
        }

        return $allRows;
    }

    public function insertTranslatedDataset($tableName, $insertArray) {

        /* @var $connection \TYPO3\CMS\Core\Database\Connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);

        $connection->insert(
            $tableName,
            $insertArray
        );
        $lastInsertId = (int)$connection->lastInsertId();
        $this->generateSlug($lastInsertId, $tableName);

        return $lastInsertId;
    }

    public function copyCategoryConnectionsForTranslation($datasetTableName, $uidForeign, $uidForeignNew) {

        $tableName = 'sys_category_record_mm';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName)->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->eq('fieldname', $queryBuilder->createNamedParameter('categories')),
                $queryBuilder->expr()->eq('tablenames', $queryBuilder->createNamedParameter($datasetTableName)),
                $queryBuilder->expr()->eq('uid_foreign', $queryBuilder->createNamedParameter($uidForeign, Connection::PARAM_INT))
            );

        $result = $queryBuilder->executeQuery();

        /* @var $connection \TYPO3\CMS\Core\Database\Connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);

        while ($row = $result->fetchAssociative()) {
            $row['uid_foreign'] = $uidForeignNew;

            $connection->insert(
                $tableName,
                $row
            );
        }
    }

    public function generateSlug($uid, $tableName) {

        $uniqueSlug = SlugUtility::generateUniqueSlug(
            $uid,
            $tableName,
            'slug'
        );

        if($uniqueSlug) {
            /* @var $connection \TYPO3\CMS\Core\Database\Connection */
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($tableName);

            $connection->update(
                $tableName,
                [
                    'slug' => $uniqueSlug
                ],
                ['uid' => $uid]
            );
        }
    }

    /*
     * TCCD - Module functions end
     */
}