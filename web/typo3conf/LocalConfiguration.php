<?php
return [
    'BE' => [
        'debug' => true,
        'explicitADmode' => 'explicitAllow',
        'installToolPassword' => '$pbkdf2-sha256$25000$hSNEbrjOABeOlMTwO6XULg$4vKkIw96dc6vIfdSERsVoh31Lklh7CNWntzOomHT7.s',
        'loginSecurityLevel' => 'rsa',
    ],
    'DB' => [
        'Connections' => [
            'Default' => [
                'charset' => 'utf8',
                'dbname' => 'database',
                'driver' => 'mysqli',
                'host' => 'db',
                'password' => 'password',
                'port' => 3306,
                'user' => 'root',
            ],
        ],
    ],
    'EXT' => [
        'extConf' => [
            'rsaauth' => 'a:1:{s:18:"temporaryDirectory";s:0:"";}',
            'saltedpasswords' => 'a:2:{s:3:"BE.";a:4:{s:21:"saltedPWHashingMethod";s:41:"TYPO3\\CMS\\Saltedpasswords\\Salt\\Pbkdf2Salt";s:11:"forceSalted";i:0;s:15:"onlyAuthService";i:0;s:12:"updatePasswd";i:1;}s:3:"FE.";a:5:{s:7:"enabled";i:1;s:21:"saltedPWHashingMethod";s:41:"TYPO3\\CMS\\Saltedpasswords\\Salt\\Pbkdf2Salt";s:11:"forceSalted";i:0;s:15:"onlyAuthService";i:0;s:12:"updatePasswd";i:1;}}',
        ],
    ],
    'FE' => [
        'debug' => true,
        'loginSecurityLevel' => 'rsa',
    ],
    'GFX' => [
        'jpg_quality' => '80',
    ],
    'MAIL' => [
        'transport' => 'sendmail',
        'transport_sendmail_command' => ' -t -i ',
        'transport_smtp_encrypt' => '',
        'transport_smtp_password' => '',
        'transport_smtp_server' => '',
        'transport_smtp_username' => '',
    ],
    'SYS' => [
        'caching' => [
            'cacheConfigurations' => [
                'cache_imagesizes' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend',
                    'options' => [
                        'defaultLifetime' => 0,
                    ],
                ],
                'extbase_datamapfactory_datamap' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend',
                    'options' => [
                        'defaultLifetime' => 0,
                    ],
                ],
                'extbase_object' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend',
                    'frontend' => 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend',
                    'groups' => [
                        'system',
                    ],
                    'options' => [
                        'defaultLifetime' => 0,
                    ],
                ],
                'extbase_reflection' => [
                    'backend' => 'TYPO3\\CMS\\Core\\Cache\\Backend\\SimpleFileBackend',
                    'options' => [
                        'defaultLifetime' => 0,
                    ],
                ],
            ],
        ],
        'devIPmask' => '*',
        'displayErrors' => 1,
        'enableDeprecationLog' => 'file',
        'encryptionKey' => '588c91d6d544e18cade1a991756627cb6d136e1abf1a095262e91658531dc951c43752c9a4f5dac8897e06102d86d21a',
        'exceptionalErrors' => 28674,
        'isInitialDatabaseImportDone' => true,
        'isInitialInstallationInProgress' => false,
        'sitename' => 'typo3api test environment',
        'sqlDebug' => 1,
        'systemLogLevel' => 0,
    ],
];
