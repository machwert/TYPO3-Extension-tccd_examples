<?php
return [
    'frontend' => [
        'machwert/redirecttotcanonical' => [
            'target' => \Machwert\TccdExamples\Middleware\RedirectToCanonical::class,
            'after' => [
                'typo3/cms-core/response-propagation',
            ],
        ],
    ],
];