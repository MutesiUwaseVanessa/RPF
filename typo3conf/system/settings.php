<?php
return [
    'BE' => [
        'debug' => false,
        'installToolPassword' => '$argon2i$v=19$m=65536,t=16,p=1$clJpTi52RnB4T2lmTlRxaQ$GX0ZzjN4d2FJKWOPG1SE7mFWsLZRlZUP/JYxKw1bq+s',
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8mb4',
                'dbname' => 'RPF',
                'driver' => 'mysqli',
                'host' => 'localhost',
                'password' => 'Yourpassword678@',
                'tableoptions' => [
                    'charset' => 'utf8mb4',
                    'collate' => 'utf8mb4_unicode_ci',
                ],
                'user' => 'RPFAdmin',
            ],
        ],
    ],
    'EXTENSIONS' => [
        'backend' => [
            'backendFavicon' => '',
            'backendLogo' => '',
            'loginBackgroundImage' => '',
            'loginFootnote' => '',
            'loginHighlightColor' => '',
            'loginLogo' => '',
            'loginLogoAlt' => '',
        ],
        'extensionmanager' => [
            'automaticInstallation' => '1',
            'offlineMode' => '0',
        ],
        'gridelements' => [
            'additionalStylesheet' => '',
            'disableAutomaticUnusedColumnCorrection' => '0',
            'disableCopyFromPageButton' => '0',
            'disableDragInWizard' => '0',
            'nestingInListModule' => '0',
            'overlayShortcutTranslation' => '0',
        ],
        't3sbootstrap' => [
            'animateCss' => '0',
            'bootswatch' => 'none',
            'cTypeClass' => '0',
            'chapter' => '0',
            'color' => '1',
            'container' => '1',
            'cssCodeEditor' => '0',
            'customHeaderClass' => '',
            'customScss' => '0',
            'customSubtitleColor' => '',
            'customTitleColor' => '',
            'editScss' => '0',
            'extNews' => '1',
            'extraStyle' => '0',
            'figureClass' => '',
            'flexformExtend' => '0',
            'flexformMinCol' => '0',
            'flexformModify' => '0',
            'flexformNoDefault' => '0',
            'fontawesomeCss' => '0',
            'fontawesomepagetitle' => '1',
            'footerInfo' => '1',
            'imageClass' => '',
            'imgCopyright' => '0',
            'imgtag' => '0',
            'keepVariables' => '0',
            'lazyLoad' => '0',
            'linkHoverEffect' => '0',
            'origimage' => '0',
            'preview' => '1',
            'previewClosedCollapsible' => '0',
            'ratio' => '0',
            'sectionOrder' => '0',
            'sitepackage' => '0',
            'spacing' => '0',
            't3sbconcatenate' => '0',
            't3sbminify' => '0',
        ],
    ],
    'FE' => [
        'cacheHash' => [
            'enforceValidation' => true,
        ],
        'debug' => false,
        'disableNoCacheParameter' => true,
        'passwordHashing' => [
            'className' => 'TYPO3\\CMS\\Core\\Crypto\\PasswordHashing\\Argon2iPasswordHash',
            'options' => [],
        ],
    ],
    'LOG' => [
        'TYPO3' => [
            'CMS' => [
                'deprecations' => [
                    'writerConfiguration' => [
                        'notice' => [
                            'TYPO3\CMS\Core\Log\Writer\FileWriter' => [
                                'disabled' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'MAIL' => [
        'transport' => 'sendmail',
        'transport_sendmail_command' => '/usr/sbin/sendmail -t -i',
        'transport_smtp_encrypt' => '',
        'transport_smtp_password' => '',
        'transport_smtp_server' => '',
        'transport_smtp_username' => '',
    ],
    'SYS' => [
        'UTF8filesystem' => true,
        'caching' => [
            'cacheConfigurations' => [
                'hash' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                ],
                'imagesizes' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
                'pages' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
                'rootline' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend',
                    'options' => [
                        'compression' => true,
                    ],
                ],
            ],
        ],
        'devIPmask' => '',
        'displayErrors' => 0,
        'encryptionKey' => '7064044da7ff1d65129b01f8589a648cfb1f454e4be9f41f497f1861b134beabc1ba100a1be68298e3af921bfd83cdb7',
        'exceptionalErrors' => 4096,
        'features' => [
            'security.backend.enforceContentSecurityPolicy' => true,
            'security.usePasswordPolicyForFrontendUsers' => true,
        ],
        'sitename' => 'RPF',
        'systemMaintainers' => [
            1,
        ],
    ],
];
