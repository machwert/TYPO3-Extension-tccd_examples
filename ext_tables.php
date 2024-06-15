<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'TccdExamples',
        'web',
        'tccdmodule',
        '',
        [
            \Machwert\TccdExamples\Controller\TccdController::class => 'list, new, edit, delete, translation',
            
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:tccd_examples/Resources/Public/Icons/user_mod_tccdmodule.svg',
            'labels' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_tccdmodule.xlf',
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_tccdexamples_domain_model_tccd', 'EXT:tccd_examples/Resources/Private/Language/locallang_csh_tx_tccdexamples_domain_model_tccd.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_tccdexamples_domain_model_tccd');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'tx_tccdexamples_domain_model_tccd',
        'categories'
    );
})();