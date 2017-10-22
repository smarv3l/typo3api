<?php

namespace Typo3Api\Tca\Field;

use PHPUnit\Framework\TestCase;

class AbstractFieldTest extends TestCase
{
    const STUB_DB_TYPE = "varchar(32) DEFAULT '' NOT NULL";

    const DEFAULT_OPTIONS = [
        'name' => 'field_1',
        'label' => 'Field 1',
        'exclude' => false,
        'localize' => true,
        'displayCond' => null,
        'useAsLabel' => false,
        'searchField' => false,
        'useForRecordType' => false,
        'index' => false,
        'dbType' => self::STUB_DB_TYPE,
    ];

    const DEFAULT_COLUMNS = [
        'field_1' => [
            'label' => 'Field 1',
            'config' => [],
        ]
    ];

    public function testBasicField()
    {
        $field = new AbstractFieldImplementation('field_1');

        $this->assertEquals(self::DEFAULT_OPTIONS, $field->getOptions());
        $this->assertEquals(self::DEFAULT_COLUMNS, $field->getColumns('some_table'));
        $this->assertEquals(
            ['some_table' => ["`field_1` " . self::STUB_DB_TYPE]],
            $field->getDbTableDefinitions('some_table')
        );
    }

    public static function invalidNameProvider()
    {
        return [
            [''],
            ['CamelCase'],
            ['this_string_is_longer_than_64_characters_which_is_awfully_long_for_a_column_anyways'],
            ['only$basic$latin'],
        ];
    }

    /**
     * @expectedException \Typo3Api\Exception\TcaFieldException
     * @dataProvider invalidNameProvider
     * @param mixed $name
     */
    public function testInvalidName($name)
    {
        new AbstractFieldImplementation($name);
    }

    public static function nameAndLabelProvider()
    {
        return [
            ['name', "Name"],
            ['longer_name', "Longer name"],
            ['cat5', "Cat5"],
            ['cat_5', "Cat 5"],
        ];
    }

    /**
     * @dataProvider nameAndLabelProvider
     * @param string $name
     * @param string $expectedLabel
     */
    public function testLabelGeneration(string $name, string $expectedLabel)
    {
        $field = new AbstractFieldImplementation($name);
        $this->assertEquals($expectedLabel, $field->getOption('label'));
    }

    public function testIndex()
    {
        $fieldName = 'field_1';
        $field = new AbstractFieldImplementation($fieldName, ['index' => true]);
        $this->assertEquals(
            [
                'some_table' => [
                    "`field_1` " . self::STUB_DB_TYPE,
                    "INDEX `$fieldName`(`$fieldName`)"
                ]
            ],
            $field->getDbTableDefinitions('some_table')
        );
    }

    public function testLocalize()
    {
        $field = new AbstractFieldImplementation('field_1', ['localize' => false]);
        $this->assertEquals([
            'field_1' => [
                'label' => 'Field 1',
                'config' => [],
                'l10n_mode' => 'exclude',
                'l10n_display' => 'defaultAsReadonly',
            ]
        ], $field->getColumns('test_table'));
    }
}
