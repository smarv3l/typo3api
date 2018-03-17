[![Build Status](https://travis-ci.org/Nemo64/typo3api.svg?branch=master)](https://travis-ci.org/Nemo64/typo3api)
[![Latest Stable Version](https://poser.pugx.org/nemo64/typo3api/v/stable)](https://packagist.org/packages/nemo64/typo3api)
[![Total Downloads](https://poser.pugx.org/nemo64/typo3api/downloads)](https://packagist.org/packages/nemo64/typo3api)
[![License](https://poser.pugx.org/nemo64/typo3api/license)](https://packagist.org/packages/nemo64/typo3api)

# apis for easier typo3 handling

This library abstracts some of the array configuration nessesary to get things done in typo3. This will result in faster, easier and less annoying workflows. 

# how to install

Install the library via composer using `composer require nemo64/typo3api` and you are done. This is a library and it doesn't require an extension installation.

# how to use

This library expects you to create your own extensions. Currently there are 2 apis.
The `TableBuilder` and the `ContentElementBuilder`.

## TableBuilder

Create the tca file in your extension like `Configuration/TCA/tx_ext_person.php`.
Than, instead of returning the a tca array, you can use the TableBuilder.

```PHP
\Typo3Api\Builder\TableBuilder::createFullyNamed('tx_ext_person')
    ->configure(new \Typo3Api\Tca\LanguageConfiguration())
    ->configure(new \Typo3Api\Tca\EnableColumnsConfiguration())
    ->configure(new \Typo3Api\Tca\SortingConfiguration())
    ->configure(new \Typo3Api\Tca\Field\InputField('first_name', ['required' => true, 'localize' => false]))
    ->configure(new \Typo3Api\Tca\Field\InputField('last_name', ['required' => true, 'localize' => false]))
    ->configure(new \Typo3Api\Tca\Field\DateField('birthday'))
    ->configure(new \Typo3Api\Tca\Field\TextareaField('notice'))
;
```

This will create the following tca configuration for you.

```PHP
[
    'ctrl' => [
        'deleted' => 'deleted',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'origUid' => 'origUid',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l10n_source',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
            'fe_group' => 'fe_group',
        ],
        'editlock' => 'editlock',
        'sortby' => 'sorting',
        'label' => 'first_name',
        'searchFields' => 'first_name, last_name',
        'label_alt' => 'last_name',
    ],
    'interface' => [
        'showRecordFieldList' => '',
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    0 => [
                        0 => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        1 => -1,
                        2 => 'flags-multiple',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'l18n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ],
        'l18n_parent' => [
            'exclude' => true,
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    0 => [
                        0 => '',
                        1 => 0,
                    ],
                ],
                'foreign_table' => 'tx_test_person',
                'foreign_table_where' => 'AND tx_test_person.pid=###CURRENT_PID### AND tx_test_person.sys_language_uid IN (-1,0)',
                'default' => 0,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    1 => [
                        0 => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:hidden.I.0',
                    ],
                ],
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'default' => 0,
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'default' => 0,
                'range' => [
                    'upper' => 2145916800,
                ],
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'fe_group' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 5,
                'maxitems' => 20,
                'items' => [
                    0 => [
                        0 => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hide_at_login',
                        1 => -1,
                    ],
                    1 => [
                        0 => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.any_login',
                        1 => -2,
                    ],
                    2 => [
                        0 => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.usergroups',
                        1 => '--div--',
                    ],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
                'enableMultiSelectFilterTextfield' => true,
            ],
        ],
        'editlock' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check',
                'items' => [
                    1 => [
                        0 => 'LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],
        'sorting' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'first_name' => [
            'label' => 'First name',
            'config' => [
                'type' => 'input',
                'size' => 25,
                'max' => 50,
                'eval' => 'trim,required',
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'last_name' => [
            'label' => 'Last name',
            'config' => [
                'type' => 'input',
                'size' => 25,
                'max' => 50,
                'eval' => 'trim,required',
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'birthday' => [
            'label' => 'Birthday',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'dbType' => NULL,
                'eval' => 'date',
                'range' => [],
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
        ],
        'notice' => [
            'label' => 'Notice',
            'config' => [
                'type' => 'text',
                'max' => 500,
                'rows' => 8,
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        1 => [
            'showitem' => '--div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, first_name,last_name,birthday,notice, --div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, --palette--;;language, --div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;hidden, --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access',
        ],
    ],
    'palettes' => [
        'language' => [
            'showitem' => 'sys_language_uid;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:sys_language_uid_formlabel, l18n_parent',
        ],
        'hidden' => [
            'showitem' => 'hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility',
        ],
        'access' => [
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel, endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel, --linebreak--, fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel, --linebreak--, editlock',
        ],
    ],
]
```

And it will also hook into the `ext_table.sql reading and add these definitions when you execute a schema compare:

```SQL
# note, this isn't the exact definition, this is what typo3 8's schema tool makes out of it.
# tinyint becomes smallint because dbal does not support tinyint.
# The order might not be correct either
CREATE TABLE tx_ext_person (
    `uid` INT AUTO_INCREMENT NOT NULL,
    `pid` INT DEFAULT 0 NOT NULL,
    `deleted` SMALLINT DEFAULT 0 NOT NULL,
    `tstamp` INT DEFAULT 0 NOT NULL,
    `crdate` INT DEFAULT 0 NOT NULL,
    `cruser_id` INT DEFAULT 0 NOT NULL,
    `origUid` INT DEFAULT 0 NOT NULL,
    `sys_language_uid` INT DEFAULT 0 NOT NULL,
    `l10n_state` TEXT DEFAULT NULL,
    `l10n_source` INT DEFAULT 0 NOT NULL,
    `l18n_diffsource` MEDIUMTEXT DEFAULT NULL,
    `l18n_parent` INT DEFAULT 0 NOT NULL,
    `hidden` SMALLINT DEFAULT 0 NOT NULL,
    `starttime` INT UNSIGNED DEFAULT 0 NOT NULL,
    `endtime` INT UNSIGNED DEFAULT 0 NOT NULL,
    `fe_group` VARCHAR(100) DEFAULT '0' NOT NULL,
    `editlock` SMALLINT DEFAULT 0 NOT NULL,
    `sorting` INT DEFAULT 0 NOT NULL,
    `first_name` VARCHAR(50) DEFAULT '' NOT NULL,
    `last_name` VARCHAR(50) DEFAULT '' NOT NULL,
    `birthday` INT DEFAULT NULL,
    `notice` VARCHAR(500) DEFAULT NULL,
    INDEX `pid` (pid),
    INDEX `language` (l18n_parent, sys_language_uid),
    INDEX `sorting` (pid, sorting),
    PRIMARY KEY(uid)
);
```

So you only have to tell the api what you want and it will abstract all the junk away from you.

## ContentElementBuilder

This is an extension of the TableBuilder. It will also do some magic to create content elements.

```PHP
\Typo3Api\Builder\ContentElementBuilder::create($_EXTKEY, 'vcard')
    ->setTitle("VCard")
    ->setDescription("Shows information about a tx_ext_person")
    // reuse the header palette
    ->configure(new \Typo3Api\Tca\ShowitemConfiguration(
        '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header'
    ))
    // this field is new and doesn't exist on tt_content yet
    ->configure(new \Typo3Api\Tca\Field\SelectRelationField('tx_ext_person', [
        'label' => 'Person',
        'foreign_table' => 'tx_ext_person'
    ]))
;
```

So this will extend the `tt_content` TCA similar to this.

```PHP
$GLOBALS['TCA']['tt_content']['columns']['tx_ext_person'] => [
    'label' => 'Person',
    'config' => [
        'foreign_table' => 'tx_ext_person',
        'foreign_table_where' => 'AND tx_ext_person.sys_language_uid IN (0, -1) ORDER BY tx_ext_person.sorting',
        'items' => [],
        'renderType' => 'selectSingle',
        'type' => 'select',
    ],
    'l10n_display' => 'defaultAsReadonly',
    'l10n_mode' => 'exclude'
];
$GLOBALS['TCA']['tt_content']['types']['vcard'] = [
    'showitem' => '--div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, --palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general,--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header,tx_ext_person, --div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, --palette--;;language, --div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;hidden, --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access, --div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories, categories, --div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes, rowDescription'
];
$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = ['VCard', 'vcard', 'content-text'];
```

The it'll add the one new column

```SQL
ALTER TABLE `tt_content` ADD `tx_ext_person` INT DEFAULT 0 NOT NULL
```

And it will also add some tsconfig to add the content element to the create wizard

```
mod.wizards.newContentElement.wizardItems.$this->section.elements.vcard {
    iconIdentifier = content-text
    title = VCard
    description = Shows information about a tx_ext_person
    tt_content_defValues {
        CType = vcard
    }
}
mod.wizards.newContentElement.wizardItems.$this->section.show := addToList(vcard)
```
 

# how to contribute

Checkout this repo and install the composer dependencies using `composer update`.
I don't ship a `composer.lock` since this library must run with the newest dependencies.
If you don't have composer locally use `make install` (or use it anyways). 

## run the unit tests

run `make test`

## run the shipped typo3 instance

Some features can't easily be tested without a running typo3 instance and simply looking at the result.
To test the interface use the included typo3 instance.

Run `make serve` and then access `localhost:8080` (opens automatically on mac).
You can then modyfy the hn_template extension
