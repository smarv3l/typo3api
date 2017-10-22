<?php

namespace Typo3Api\Tca\Field;

use PHPUnit\Framework\TestCase;

class AbstractFieldTest extends TestCase
{
    const STUB_DB_TYPE = "varchar(32) DEFAULT '' NOT NULL";

    public static function validNameProvider()
    {
        return [
            ['field_1', 'field_2'],
            ['first_name', 'last_name']
        ];
    }

    /**
     * @param AbstractField $field
     */
    public function assertBasicCtrlChange(AbstractField $field)
    {
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'stub_table');
        $this->assertEmpty($ctrl);
    }

    /**
     * @param AbstractField $field
     */
    private function assertBasicColumns(AbstractField $field)
    {
        $this->assertEquals([
            $field->getName() => [
                'label' => $field->getOption('label'),
                'config' => [],
            ]
        ], $field->getColumns('stub_table'));
    }

    /**
     * @param AbstractField $field
     */
    public function assertBasicPalette(AbstractField $field)
    {
        $this->assertEmpty($field->getPalettes('stub_table'));
    }

    /**
     * @param AbstractField $field
     */
    public function assertBasicShowItem(AbstractField $field)
    {
        $this->assertEquals($field->getName(), $field->getShowItemString('stub_table'));
    }

    /**
     * @param AbstractField $field
     */
    public function assertBasicDatabase(AbstractField $field)
    {
        $fieldName = $field->getName();
        $this->assertEquals(
            ['stub_table' => ["`$fieldName` " . self::STUB_DB_TYPE]],
            $field->getDbTableDefinitions('stub_table')
        );
    }

    /**
     * @dataProvider validNameProvider
     */
    public function testBasicField($fieldName)
    {
        $field = new AbstractFieldImplementation($fieldName);

        $this->assertBasicCtrlChange($field);
        $this->assertBasicColumns($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);
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

        $this->assertBasicCtrlChange($field);
        $this->assertBasicColumns($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testIndex(string $fieldName)
    {
        $field = new AbstractFieldImplementation($fieldName, ['index' => true]);

        $this->assertBasicCtrlChange($field);
        $this->assertBasicColumns($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertEquals(
            [
                'some_table' => [
                    "`$fieldName` " . self::STUB_DB_TYPE,
                    "INDEX `$fieldName`(`$fieldName`)"
                ]
            ],
            $field->getDbTableDefinitions('some_table')
        );
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testExclude(string $fieldName)
    {
        $field = new AbstractFieldImplementation($fieldName, ['exclude' => false]);
        $this->assertArrayNotHasKey('exclude', $field->getColumns('stb_table')[$fieldName]);
        $this->assertBasicCtrlChange($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);

        $field = new AbstractFieldImplementation($fieldName, ['exclude' => true]);
        $this->assertEquals(1, $field->getColumns('stb_table')[$fieldName]['exclude']);
        $this->assertBasicCtrlChange($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testLocalize(string $fieldName)
    {
        $field = new AbstractFieldImplementation($fieldName, ['localize' => true]);
        $this->assertBasicCtrlChange($field);
        $this->assertArrayNotHasKey('l10n_mode', $field->getColumns('stb_table')[$fieldName]);
        $this->assertArrayNotHasKey('l10n_display', $field->getColumns('stb_table')[$fieldName]);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);

        $field = new AbstractFieldImplementation($fieldName, ['localize' => false]);
        $this->assertBasicCtrlChange($field);
        $this->assertEquals('exclude', $field->getColumns('stb_table')[$fieldName]['l10n_mode']);
        $this->assertEquals('defaultAsReadonly', $field->getColumns('stb_table')[$fieldName]['l10n_display']);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testDisplayCondition(string $fieldName)
    {
        $field = new AbstractFieldImplementation($fieldName, ['displayCond' => 'some condition']);
        $this->assertBasicCtrlChange($field);
        $this->assertEquals('some condition', $field->getColumns('stb_table')[$fieldName]['displayCond']);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testUseAsLabel(string $fieldName)
    {
        $field = new AbstractFieldImplementation($fieldName, ['useAsLabel' => true]);

        $ctrl = [];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals([
            'label' => $fieldName
        ], $ctrl);
        $this->assertBasicColumns($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);

        $ctrl = ['label' => 'other_field'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals([
            'label' => 'other_field',
            'label_alt' => $fieldName
        ], $ctrl);

        $ctrl = ['label' => 'other_field_1', 'label_alt' => 'other_field_2'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals([
            'label' => 'other_field_1',
            'label_alt' => 'other_field_2, ' . $fieldName
        ], $ctrl);
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testSearchField(string $fieldName)
    {
        $field = new AbstractFieldImplementation($fieldName, ['searchField' => false]);
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEmpty($ctrl);

        $field = new AbstractFieldImplementation($fieldName, ['searchField' => true]);
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals(['search_field' => $fieldName], $ctrl);

        $field = new AbstractFieldImplementation($fieldName, ['searchField' => true]);
        $ctrl = ['search_field' => 'other_field'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals(['search_field' => 'other_field, ' . $fieldName], $ctrl);
    }
}
