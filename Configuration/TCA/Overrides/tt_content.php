<?php
defined('TYPO3') || die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'TccdExamples',
    'TccdpluginList',
    'TCCD Plugin - List'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'TccdExamples',
    'TccdpluginShow',
    'TCCD Plugin - Show'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'TccdExamples',
    'TccdpluginNew',
    'TCCD Plugin - New'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'TccdExamples',
    'TccdpluginFilter',
    'TCCD Plugin - Filter'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['tccdexamples_tccdpluginlist'] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['tccdexamples_tccdpluginlist'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'tccdexamples_tccdpluginlist',
    'FILE:EXT:tccd_examples/Configuration/Flexforms/TccdListPlugin.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['tccdexamples_tccdpluginshow'] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['tccdexamples_tccdpluginshow'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'tccdexamples_tccdpluginshow',
    'FILE:EXT:tccd_examples/Configuration/Flexforms/TccdShowPlugin.xml'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['tccdexamples_tccdpluginfilter'] = 'layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['tccdexamples_tccdpluginfilter'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'tccdexamples_tccdpluginfilter',
    'FILE:EXT:tccd_examples/Configuration/Flexforms/TccdFilterPlugin.xml'
);

// Backend plugin preview with record list [A]
$GLOBALS['TCA']['tt_content']['types']['list']['previewRenderer']['tccdexamples_tccdpluginlist']
    = Machwert\TccdExamples\Preview\MyPreviewRenderer::class;