<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Site Package',
    'description' => 'Custom site package for TYPO3 v12',
    'category' => 'templates',
    'state' => 'stable',
    'author' => 'Your Name',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.4.99',
        ]
    ]
];
