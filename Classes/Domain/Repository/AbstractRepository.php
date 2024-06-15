<?php

namespace Machwert\TccdExamples\Domain\Repository;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;


/**
 * Class AbstractRepository brings methods for query debugging in TYPO3 ver. 10.x
 * Based on StackOverflow answer by `pgampe` answer and `FranzHolzinger` comment
 * source https://stackoverflow.com/a/44286155/1066240
 *
 * All repositories in this extension should extend it.
 *
 * @author Marcus Biesioroff <biesior@gmail.com>
 * @package Machwert\TccdExamples\Domain\Repository
 */
abstract class AbstractRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{


    /**
     * @param mixed       $query TYPO3\CMS\Core\Database\Query\QueryBuilder | TYPO3\CMS\Extbase\Persistence\Generic\Query
     * @param string|null $title Optional title for var_dump()
     * @param bool        $replaceParams if true replaces the params in SQL statement with values, otherwise dumps the array of params. @see self::renderDebug()
     *
     * @throws Exception
     */
    protected function debugQuery($query, string $title = null, bool $replaceParams = true): void
    {
        if ($query instanceof \TYPO3\CMS\Core\Database\Query\QueryBuilder) {
            $sql = $query->getSQL();
            $params = $query->getParameters();
            $this->renderDebug($sql, $params, $title, $replaceParams);
        } elseif ($query instanceof \TYPO3\CMS\Extbase\Persistence\Generic\Query) {
            $this->parseTheQuery($query, $title, $replaceParams);
        } else {
            throw new Exception('Unhandled type for SQL query, curently only TYPO3\CMS\Core\Database\Query\QueryBuilder | TYPO3\CMS\Extbase\Persistence\Generic\Query can be debugged with ' . static::getRepositoryClassName() . '::debugQuery() method.', 1596458998);
        }
    }

    /**
     * Parses query and displays debug
     *
     * @param QueryInterface $query Query
     * @param string|null    $title Optional title
     * @param bool           $replaceParams if true replaces the params in SQL statement with values, otherwise dumps the array of params. @see self::renderDebug()
     */
    private function parseTheQuery(QueryInterface $query, string $title = null, $replaceParams = true): void
    {
        // /** @var Typo3DbQueryParser $queryParser */
        $queryParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser::class);
        //$queryParser = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser::class);

        //$queryBuilder = $typo3DbQueryParser->convertQueryToDoctrineQueryBuilder($query);

        $sql = $queryParser->convertQueryToDoctrineQueryBuilder($query)->getSQL();
        $params = $queryParser->convertQueryToDoctrineQueryBuilder($query)->getParameters();
        $this->renderDebug($sql, $params, $title, $replaceParams);

    }


    /**
     * Renders the output with DebuggerUtility::var_dump()
     *
     * @param string      $sql Generated SQL
     * @param array       $params Params' array
     * @param string|null $title Optional title for var_dump()
     * @param bool        $replaceParams if true replaces the params in SQL statement with values, otherwise dumps the array of params.
     */
    private function renderDebug(string $sql, array $params, string $title = null, bool $replaceParams = true): void
    {
        if ($replaceParams) {

            $search = array();
            $replace = array();
            foreach ($params as $k => $v) {
                $search[] = ':' . $k;
                $type = gettype($v);
                if (in_array($type, ['integer'])) {
                    $replace[] = $v;
                } else {
                    $replace[] = '\'' . $v . '\'';
                }
            }
            $sql = str_replace($search, $replace, $sql);
            echo($sql);
            //DebuggerUtility::var_dump($sql, $title);
        } else {
            echo($sql);
            /* DebuggerUtility::var_dump(
                [
                    'SQL'        => $sql,
                    'Parameters' => $params
                ],
                $title);
            */
        }
    }
}
