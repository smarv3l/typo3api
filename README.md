

```PHP
\Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'person')
    ->configure(new \Typo3Api\Tca\LanguageConfiguration())
    ->configure(new \Typo3Api\Tca\EnableColumnsConfiguration())
    ->configure(new \Typo3Api\Tca\SortingConfiguration())
    ->configure(new \Typo3Api\Tca\CustomConfiguration([
        'ctrl' => [
            'label_userFunc' => function (&$parameters) {
                $name = $parameters['row']['last_name'];
                $name .= ', ' . $parameters['row']['first_name'];
                $name .= ' (' . $parameters['row']['email'] . ')';
                $parameters['title'] = $name;
            }
        ]
    ]))
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
        new \Typo3Api\Tca\Field\InputField('first_name', ['localize' => false]),
        new \Typo3Api\Tca\Field\InputField('last_name', ['localize' => false]),
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
    ->configureInTab('Media', new \Typo3Api\Tca\Field\ImageField('image'))
    ->configureInTab('Media', new \Typo3Api\Tca\Field\MediaField('media'))
    ->configureInTab('Contact', new \Typo3Api\Tca\Field\EmailField('email', ['unique' => true]))
    ->configureInTab('Contact', new \Typo3Api\Tca\Field\LinkField('website'))
    ->configureInTab('Contact', new \Typo3Api\Tca\Palette('phone', [
        new \Typo3Api\Tca\Field\PhoneField('work'),
        new \Typo3Api\Tca\Field\PhoneField('home'),
        new \Typo3Api\Tca\Field\PhoneField('mobile'),
    ]))
    ->configureInTab('Address', new \Typo3Api\Tca\Field\IrreField('addresses', [
        'foreign_table' => \Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'address')
            ->configure(new \Typo3Api\Tca\SortingConfiguration())
            ->configure(new \Typo3Api\Tca\Field\InputField('city'))
            ->configure(new \Typo3Api\Tca\Palette('full_street', [
                new \Typo3Api\Tca\Field\InputField('street'),
                new \Typo3Api\Tca\Field\InputField('number', ['max' => 5]),
            ]))
            ->configure(new \Typo3Api\Tca\Field\InputField('country'))
    ]))
;

\Typo3Api\Builder\TableBuilder::createForType($_EXTKEY, 'person', '2')
    ->inheritConfigurationFromType('1')
    ->addOrMoveTabInFrontOfTab('Job', 'Media')
    ->configureInTab('Job', new \Typo3Api\Tca\Field\InputField('position'))
    ->configureInTab('Job', new \Typo3Api\Tca\Field\Double2Field('salary', ['max' => 100000]))
;

\Typo3Api\Builder\ContentElementBuilder::create($_EXTKEY, 'vcard')
    ->setTitle("VCard")
    ->setDescription("Shows a Persons vcard ~ define a person in a storage folder")
    // options will be ignored if the column already exists
    // this is a limitation i want to remove in the future
    ->configure(new \Typo3Api\Tca\Field\InputField('header'))
    ->configure(new \Typo3Api\Tca\Field\SelectRelationField('person', [
        'foreign_table' => 'tx_hntemplates_person'
    ]))
;
```