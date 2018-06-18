[![Build Status](https://travis-ci.org/Nemo64/typo3api.svg?branch=master)](https://travis-ci.org/Nemo64/typo3api)
[![Latest Stable Version](https://poser.pugx.org/nemo64/typo3api/v/stable)](https://packagist.org/packages/nemo64/typo3api)
[![Total Downloads](https://poser.pugx.org/nemo64/typo3api/downloads)](https://packagist.org/packages/nemo64/typo3api)
[![License](https://poser.pugx.org/nemo64/typo3api/license)](https://packagist.org/packages/nemo64/typo3api)

# apis for easier typo3 handling

This library abstracts some of the array configuration necessary to get things done in typo3. This will result in faster, easier and less annoying workflows. 

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

## ContentElement

To Create a content element, use the TableBuilder inside `Configuration/TCA/Override/tt_content.php`.

```PHP
\Nemo64\Typo3Api\Builder\TableBuilder::create('tt_content', 'carousel')
    ->configure(new \Nemo64\Typo3Api\Tca\ContentElement())
    // add more fields as you like
;
```
Or with more options.
```PHP
\Nemo64\Typo3Api\Builder\TableBuilder::create('tt_content', 'quote')
    ->configure(new \Nemo64\Typo3Api\Tca\ContentElement([
        'name' => 'Quote element',
        'description' => 'Tell what other peaple are saying',
        'icon' => 'content-quote',
        'headline' => 'hidden', // adds only the headline field
    ]))
;
```

## run the unit tests

run `vendor/bin/phpunit`
