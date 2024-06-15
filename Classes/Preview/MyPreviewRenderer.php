<?php
declare(strict_types=1);
namespace Machwert\TccdExamples\Preview;

use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

// Backend plugin preview with record list [A]

class MyPreviewRenderer extends StandardContentPreviewRenderer
{
    const TABLE_NAME = 'tt_content';

    protected string $template = 'EXT:tccd_examples/Resources/Private/Templates/Tccd/BackendPreview/List.html';

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $languageService = $this->getLanguageService();
        $record = $item->getRecord();
        $out = '';
        $hookOut = '';
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'])) {
            $pageLayoutView = PageLayoutView::createFromPageLayoutContext($item->getContext());
            $_params = ['pObj' => &$pageLayoutView, 'row' => $record];
            foreach (
                $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$record['list_type']] ??
                $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['_DEFAULT'] ??
                [] as $_funcRef
            ) {
                $hookOut .= GeneralUtility::callUserFunction($_funcRef, $_params, $pageLayoutView);
            }
        }

        if ((string)$hookOut !== '') {
            $out .= $hookOut;
        } elseif (!empty($record['list_type'])) {
            $label = BackendUtility::getLabelFromItemListMerged($record['pid'], 'tt_content', 'list_type', $record['list_type']);
            if (!empty($label)) {
                $out .= $this->linkEditContent('<strong>' . htmlspecialchars($languageService->sL($label)) . '</strong>', $record) . '<br />';
            } else {
                $message = sprintf($languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.noMatchingValue'), $record['list_type']);
                $out .= '<span class="label label-warning">' . htmlspecialchars($message) . '</span>';
            }
        } else {
            $out .= '<strong>' . $languageService->getLL('noPluginSelected') . '</strong>';
        }
        $out .= htmlspecialchars($languageService->sL(BackendUtility::getLabelFromItemlist('tt_content', 'pages', $record['pages']))) . '<br />';


        $sysFolders = $record['pages'];
        $sysFolderArray = explode(",", $sysFolders);

        // Doctrine DBAL [3]
        // Using the QueryBuilder [8]
        $table = 'tx_tccdexamples_domain_model_tccd';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table)->createQueryBuilder();

        $queryBuilder
            ->select('uid','title')
            ->from($table)
            ->where(
                $queryBuilder->expr()->in('pid',
                    $queryBuilder->createNamedParameter(
                        $sysFolderArray,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->setMaxResults(16)
            ->orderBy('sorting');

        $result = $queryBuilder->executeQuery();

        $datasetCount = 0;
        while ($row = $result->fetchAssociative()) {
            $out .= $row['title'].'<br>';
            $datasetCount++;
            if ($datasetCount == 15) {
                $out .= '...';
                break;
            }
        }
        if($datasetCount == 0) {
            $out .= 'No data available.<br>Please check "Record Storage Page [pages]" in plugin settings.';
        }
        $identifier = $record['uid'];
        $fluidContent = $this->getFluidContent($out, $identifier);
        return $fluidContent;
    }

    /**
     * @return string
     */
    protected function getFluidContent($data, $identifier): string
    {
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename($this->template);
        $standaloneView->assignMultiple([
            'data' => $data,
            'identifier' => $identifier
        ]);
        return $standaloneView->render();
    }
}