<?php

use Machwert\TccdExamples\Hooks\OwnCanonicalGenerator;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') || die();

# Enable preview from TYPO3 backend for datasets [B]
$versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
// Only include page.tsconfig if TYPO3 version is below 12 so that it is not imported twice.
if ($versionInformation->getMajorVersion() < 12) {
    ExtensionManagementUtility::addPageTSConfig(
        '@import "EXT:tccd_examples/Configuration/page.tsconfig"'
    );
}

//$renderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
//$renderer->addJsFile('EXT:tccd_examples/Resources/Public/JavaScript/Backend.js', 'text/javascript', false, false, '', true,  '|', false, '');


// Extending Existing Functionality [16] - Hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['tccd_examples']
    = \Machwert\TccdExamples\Hooks\MyDataHandlerHooks::class;

/*
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\CMS\Frontend\Page\PageGenerator']['generateMetaTags']['canonical'] =
    OwnCanonicalGenerator::class . '->generateChild';
*/

# Extending Existing Functionality [16] - 3-2 userFuncs in the TCA
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1709773998] = [
    'nodeName' => 'specialLinkfieldElement',
    'priority' => 40,
    'class' => \Machwert\TccdExamples\Form\Element\SpecialLinkfieldElement::class,
];

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'TccdExamples',
        'TccdpluginList',
        [
            \Machwert\TccdExamples\Controller\TccdController::class => 'list, search'
        ],
        // non-cacheable actions
        [
            \Machwert\TccdExamples\Controller\TccdController::class => 'search'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'TccdExamples',
        'TccdpluginShow',
        [
            \Machwert\TccdExamples\Controller\TccdController::class => 'show'
        ],
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'TccdExamples',
        'TccdpluginNew',
        [
            \Machwert\TccdExamples\Controller\TccdController::class => 'new, create, edit'
        ],
        // non-cacheable actions
        [
            \Machwert\TccdExamples\Controller\TccdController::class => 'new, create, edit'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'TccdExamples',
        'TccdpluginFilter',
        [
            \Machwert\TccdExamples\Controller\TccdController::class => 'filter'
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    tccdplugin {
                        iconIdentifier = tccd_examples-plugin-tccdplugin
                        title = LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccd_examples_tccdplugin.name
                        description = LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccd_examples_tccdplugin.description
                        tt_content_defValues {
                            CType = list
                            list_type = tccdexamples_tccdplugin
                        }
                    }
                }
                show = *
            }
       }'
    );
})();
