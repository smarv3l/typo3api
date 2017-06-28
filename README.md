

```PHP
\Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'person')
    ->configure(new \Typo3Api\Tca\MetaFieldsConfiguration())
    ->configure(new \Typo3Api\Tca\LanguageConfiguration())
    ->configure(new \Typo3Api\Tca\EnableFieldConfiguration())
    ->configure(new \Typo3Api\Tca\SortingConfiguration())
    ->configure(new \Typo3Api\Tca\PaletteConfiguration('name', [
        new \Typo3Api\Tca\Field\SelectField('gender', [
            'label' => 'Salutation',
            'items' => [
                ['Mr.', 'm'],
                ['Ms.', 'f'],
            ]
        ]),
        new \Typo3Api\Tca\LinebreakConfiguration(),
        new \Typo3Api\Tca\Field\InputField('first_name'),
        new \Typo3Api\Tca\Field\InputField('last_name'),
    ]))
    ->configure(new \Typo3Api\Tca\Field\InputField('other_name'))
    ->configure(new \Typo3Api\Tca\Field\ImageField('image'))
    ->configure(new \Typo3Api\Tca\Field\TextareaField('notice'))
    ->configure(new \Typo3Api\Tca\Field\IrreField('addresses', [
        'foreignTable' => \Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'address')
            ->configure(new \Typo3Api\Tca\MetaFieldsConfiguration())
            ->configure(new \Typo3Api\Tca\SortingConfiguration())
            ->configure(new \Typo3Api\Tca\Field\InputField('city'))
            ->configure(new \Typo3Api\Tca\PaletteConfiguration('full_street', [
                new \Typo3Api\Tca\Field\InputField('street'),
                new \Typo3Api\Tca\Field\InputField('number', ['max' => 5]),
            ]))
            ->configure(new \Typo3Api\Tca\Field\InputField('country'))
    ]))
    ->configure(new \Typo3Api\Tca\Field\MediaField('media'))
;
```