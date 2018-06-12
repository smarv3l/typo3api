```PHP
\Nemo64\Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'person')
    ->configure(new \Nemo64\Typo3Api\Tca\LanguageConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\EnableColumnsConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\SortingConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\CustomConfiguration([
        'ctrl' => [
            'label_userFunc' => function (&$parameters) {
                $name = $parameters['row']['last_name'];
                $name .= ', ' . $parameters['row']['first_name'];
                $name .= ' (' . $parameters['row']['email'] . ')';
                $parameters['title'] = $name;
            }
        ]
    ]))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\SelectField('type', [
        'useForRecordType' => true,
        'items' => [
            ['Normal', '1'],
            ['Employee', '2']
        ]
    ]))
    ->configure(new \Nemo64\Typo3Api\Tca\Palette('name', [
        new \Nemo64\Typo3Api\Tca\Field\SelectField('gender', [
            'label' => 'Salutation',
            'items' => [
                ['Mr.', 'm'],
                ['Ms.', 'f'],
            ]
        ]),
        new \Nemo64\Typo3Api\Tca\Linebreak(),
        new \Nemo64\Typo3Api\Tca\Field\InputField('first_name', ['localize' => false]),
        new \Nemo64\Typo3Api\Tca\Field\InputField('last_name', ['localize' => false]),
    ]))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\Double2Field('height', ['min' => 1.0, 'max' => 3.0]))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\DateField('birthday'))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\CustomField('favourite_color', [
        'dbType' => "VARCHAR(7) DEFAULT '#000000' NOT NULL",
        'localize' => false,
        'config' => [
            'type' => 'input',
            'renderType' => 'colorpicker',
            'size' => 7,
            'default' => '#000000'
        ]
    ]))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\TextareaField('notice'))
    ->configureInTab('Media', new \Nemo64\Typo3Api\Tca\Field\ImageField('image', [
        'cropVariants' => [
            'default' => '4:3'
        ]
    ]))
    ->configureInTab('Media', new \Nemo64\Typo3Api\Tca\Field\MediaField('media'))
    ->configureInTab('Contact', new \Nemo64\Typo3Api\Tca\Field\EmailField('email', ['unique' => true]))
    ->configureInTab('Contact', new \Nemo64\Typo3Api\Tca\Field\LinkField('website'))
    ->configureInTab('Contact', new \Nemo64\Typo3Api\Tca\Palette('phone', [
        new \Nemo64\Typo3Api\Tca\Field\PhoneField('work'),
        new \Nemo64\Typo3Api\Tca\Field\PhoneField('home'),
        new \Nemo64\Typo3Api\Tca\Field\PhoneField('mobile'),
    ]))
    ->configureInTab('Address', new \Nemo64\Typo3Api\Tca\Field\IrreField('addresses', [
        'localize' => false,
        'foreign_table' => \Nemo64\Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'address')
            ->configure(new \Nemo64\Typo3Api\Tca\SortingConfiguration())
            ->configure(new \Nemo64\Typo3Api\Tca\Field\InputField('city'))
            ->configure(new \Nemo64\Typo3Api\Tca\Palette('full_street', [
                new \Nemo64\Typo3Api\Tca\Field\InputField('street'),
                new \Nemo64\Typo3Api\Tca\Field\InputField('number', ['max' => 5]),
            ]))
            ->configure(new \Nemo64\Typo3Api\Tca\Field\InputField('country'))
    ]))
;

\Nemo64\Typo3Api\Builder\TableBuilder::createForType($_EXTKEY, 'person', '2')
    ->inheritConfigurationFromType('1')
    ->addOrMoveTabInFrontOfTab('Job', 'Media')
    ->configureInTab('Job', new \Nemo64\Typo3Api\Tca\Field\InputField('position'))
    ->configureInTab('Job', new \Nemo64\Typo3Api\Tca\Field\Double2Field('salary', ['max' => 100000]))
;

\Nemo64\Typo3Api\Builder\ContentElementBuilder::create($_EXTKEY, 'vcard')
    ->setTitle("VCard")
    ->setDescription("Shows a Persons vcard ~ define a person in a storage folder")
    // options will be ignored if the column already exists
    // this is a limitation i want to remove in the future
    ->configure(new \Nemo64\Typo3Api\Tca\Field\InputField('header'))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\SelectRelationField('person', [
        'foreign_table' => 'tx_hntemplates_person',
        'foreign_table_where' => 'ORDER BY tx_hntemplates_person.last_name'
    ]))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\RteField('bodytext'))
;

\Nemo64\Typo3Api\Builder\TableBuilder::create($_EXTKEY, 'company')
    ->configure(new \Nemo64\Typo3Api\Tca\EnableColumnsConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\LanguageConfiguration())
    ->configure(new \Nemo64\Typo3Api\Tca\Field\InputField('name'))
    ->configure(new \Nemo64\Typo3Api\Tca\Field\MultiSelectRelationField('employees', [
        'foreign_table' => 'tx_hntemplates_person'
    ]))
;
```