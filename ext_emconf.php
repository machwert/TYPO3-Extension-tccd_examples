<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TCCD examples',
    'description' => 'This extension tests TYPO3 functionality based on the syllabus for TYPO3 v11 for preparation for TYPO3 Certified Developer Certification (TCCD).',
    'category' => 'example',
    'author' => 'Volker Golbig',
    'author_email' => 'typo3@machwert.de',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '0.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
