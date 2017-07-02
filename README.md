

```PHP
\Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'person')
    ->configure(new \Typo3Api\Tca\MetaFieldsConfiguration())
    ->configure(new \Typo3Api\Tca\LanguageConfiguration())
    ->configure(new \Typo3Api\Tca\EnableFieldConfiguration())
    ->configure(new \Typo3Api\Tca\SortingConfiguration())
    ->configure(new \Typo3Api\Tca\Field\SelectField('type', [
        'useForRecordType' => true,
        'items' => [
            ['Normal', '1'],
            ['Employee', '2']
        ]
    ]))
    ->configure(new \Typo3Api\Tca\Palette('name', [
        new \Typo3Api\Tca\Field\SelectField('gender', [
            'label' => 'Salutation',
            'items' => [
                ['Mr.', 'm'],
                ['Ms.', 'f'],
            ]
        ]),
        new \Typo3Api\Tca\Linebreak(),
        new \Typo3Api\Tca\Field\InputField('first_name'),
        new \Typo3Api\Tca\Field\InputField('last_name'),
    ]))
    ->configure(new \Typo3Api\Tca\Palette('contact', [
        new \Typo3Api\Tca\Field\EmailField('email', ['unique' => true]),
        new \Typo3Api\Tca\Linebreak(),
        new \Typo3Api\Tca\Field\LinkField('website'),
        new \Typo3Api\Tca\Linebreak(),
        new \Typo3Api\Tca\Field\PhoneField('phone'),
    ]))
    ->configure(new \Typo3Api\Tca\Field\CustomField('favourite_color', [
        'dbType' => "VARCHAR(7) DEFAULT '#000000' NOT NULL",
        'localize' => false,
        'config' => [
            'type' => 'input',
            'renderType' => 'colorpicker',
            'size' => 7,
            'default' => '#000000'
        ]
    ]))
    ->configure(new \Typo3Api\Tca\Field\TextareaField('notice'))
    ->configure(new \Typo3Api\Tca\Field\ImageField('image'))
    ->configure(new \Typo3Api\Tca\Field\IrreField('addresses', [
        'foreignTable' => \Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'address')
            ->configure(new \Typo3Api\Tca\MetaFieldsConfiguration())
            ->configure(new \Typo3Api\Tca\SortingConfiguration())
            ->configure(new \Typo3Api\Tca\Field\InputField('city'))
            ->configure(new \Typo3Api\Tca\Palette('full_street', [
                new \Typo3Api\Tca\Field\InputField('street'),
                new \Typo3Api\Tca\Field\InputField('number', ['max' => 5]),
            ]))
            ->configure(new \Typo3Api\Tca\Field\InputField('country'))
    ]))
    ->configure(new \Typo3Api\Tca\Field\MediaField('media'))
;

\Typo3Api\Builder\TableBuilder::createForType($_EXTKEY, 'person', '2')
    ->inheritConfigurationFromType('1')
    ->configureAtPosition('after:type', new \Typo3Api\Tca\Field\InputField('position'))
;
```