<?php

/*
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'tccd_examples',
    'tx_tccdexamples_domain_model_tccd',
    'categories'
);
*/

$versionInformation = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
if ($versionInformation->getMajorVersion() > 11) {
    $imageConfig = [
        'type' => 'file',
        'maxitems' => 1,
        'allowed' => 'common-image-types'
    ];
} else {
    $imageConfig = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
        'images',
        [
            'appearance' => [
                'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference'
            ],
            'foreign_types' => [
                '0' => [
                    'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                    'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                    'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                    'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                    'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                ],
                \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                    'showitem' => '
                            --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                            --palette--;;filePalette'
                ]
            ],
            'foreign_match_fields' => [
                'fieldname' => 'images',
                'tablenames' => 'tx_tccdexamples_domain_model_tccd',
                'table_local' => 'sys_file',
            ],
            'maxitems' => 1
        ],
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
    );
}
return [
    'ctrl' => [
        'title' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title, description, syllabusdescription, slug',
        'iconfile' => 'EXT:tccd_examples/Resources/Public/Icons/tx_tccdexamples_domain_model_tccd.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'title, slug, categories, description, syllabusdescription, version, images, link, links, related_tccds, edited, makenewtranslation, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, sys_language_uid, l10n_parent, l10n_diffsource, --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, hidden, starttime, endtime'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_tccdexamples_domain_model_tccd',
                'foreign_table_where' => 'AND {#tx_tccdexamples_domain_model_tccd}.{#pid}=###CURRENT_PID### AND {#tx_tccdexamples_domain_model_tccd}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ],
                'behaviour' => [
                    'allowLanguageSynchronization' => true
                ]
            ],
        ],
        'categories' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'treeConfig' => [
                    'parentField' => 'parent',
                    'appearance' => [
                        'showHeader' => true,
                        'expandAll' => true,
                        'maxLevels' => 99,
                    ],
                ],
                'MM' => 'sys_category_record_mm',
                'MM_match_fields' => [
                    'fieldname' => 'categories',
                    'tablenames' => 'tx_tccdexamples_domain_model_tccd',
                ],
                'MM_opposite_field' => 'items',
                'foreign_table' => 'sys_category',
                'foreign_table_where' => ' AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) ORDER BY sys_category.sorting',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 99,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],

        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'default' => ''
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
            
        ],
        'syllabusdescription' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.syllabusdescription',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
                'richtextConfiguration' => 'default',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],

        ],
        'version' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.version',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['-- Please choose --', 0],
                    ['-- TYPO3 v11 --', 1],
                ],
                'size' => 1,
                'default' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ],
        ],
        'link' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.link',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputLink',
            ]
        ],
        # Extending Existing Functionality [16] - 3-2 userFuncs in the TCA
        'links' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.links',
            'config' => [
                'type' => 'user',
                'renderType' => 'specialLinkfieldElement',
            ]
        ],
        'slug' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.slug',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title'],
                    'fieldSeparator' => '-',
                    'replacements' => [
                        '/' => '',
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInPid',
            ],
            
        ],
        'edited' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.edited',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Not edited', 0],
                    ['Partly edited', 1],
                    ['Finished', 2]
                ],
                'size' => 1,
                'default' => 0,
                'maxitems' => 1,
                'eval' => 'required'
            ]
        ],
        # Include multi select related dataset form field in TCA [C]
        'related_tccds' => [
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.related_tccds',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_tccdexamples_domain_model_tccd',
                'foreign_table_where' => 'AND {#tx_tccdexamples_domain_model_tccd}.{#sys_language_uid} = ###REC_FIELD_sys_language_uid### ORDER BY title'

            ],
        ],
        'makenewtranslation' => [
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.makenewtranslation',
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => ''
                    ]
                ],
            ],
        ],
        // [4] - File abstraction layer (FAL)
        'images' => [
            'exclude' => true,
            'label' => 'LLL:EXT:tccd_examples/Resources/Private/Language/locallang_db.xlf:tx_tccdexamples_domain_model_tccd.images',
            'config' => $imageConfig
        ],
    ],
];



\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'fe_users',
    $tempColumns
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    'tx_myextension_special',
);