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
\Nemo64\Typo3Api\Builder\TableBuilder::create('tx_ext_person')
    ->configure(new \Nemo64\Typo3Api\Tca\LanguageConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\EnableColumnsConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\SortingConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\Field\InputField('first_name', ['required' => true, 'localize' => false]))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\InputField('last_name', ['required' => true, 'localize' => false]))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\DateField('birthday'))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\TextareaField('notice'))
;
```

That is all. You can now start using the tx_ext_person table.

## ContentElementBuilder

This is an extension of the TableBuilder. It will also do some magic to create content elements.

```PHP
\Nemo64\Typo3Api\Builder\ContentElementBuilder::create($_EXTKEY, 'vcard')
    ->setTitle("VCard")
    ->setDescription("Shows information about a tx_ext_person")
    // reuse the header palette
    ->configure(new \Nemo64\Typo3Api\Tca\ShowitemConfiguration(
        '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.header;header'
    ))
    // this field is new and doesn't exist on tt_content yet
    ->configure(new \Nemo64\Typo3Api\Tca\Field\SelectRelationField('tx_ext_person', [
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

## run the unit tests

run `vendor/bin/phpuniz`
