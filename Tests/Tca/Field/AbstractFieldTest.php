<?php

namespace Nemo64\Typo3Api\Tca\Field;

use PHPUnit\Framework\TestCase;

class AbstractFieldTest extends TestCase
{
    const STUB_DB_TYPE = "VARCHAR(32) DEFAULT '' NOT NULL";

    public static function validNameProvider()
    {
        return [
            ['field_1', 'field_2'],
            ['first_name', 'last_name']
        ];
    }

    /**
     * @param string $name
     * @param array $options
     * @return AbstractField
     */
    protected function createFieldInstance(string $name, array $options = []): AbstractField
    {
        return new AbstractFieldImplementation($name, $options);
    }

    /**
     * @param AbstractField $field
     */
    protected function assertBasicCtrlChange(AbstractField $field)
    {
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'stub_table');
        $this->assertEmpty($ctrl, "No modification to ctrl is done");
    }

    /**
     * @param AbstractField $field
     */
    protected function assertBasicColumns(AbstractField $field)
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
    protected function assertBasicPalette(AbstractField $field)
    {
        $this->assertEmpty($field->getPalettes('stub_table'));
    }

    /**
     * @param AbstractField $field
     */
    protected function assertBasicShowItem(AbstractField $field)
    {
        $this->assertEquals($field->getName(), $field->getShowItemString('stub_table'));
    }

    /**
     * @param AbstractField $field
     */
    protected function assertBasicDatabase(AbstractField $field)
    {
        $fieldName = $field->getName();
        $this->assertEquals(
            ['stub_table' => ["`$fieldName` " . static::STUB_DB_TYPE]],
            $field->getDbTableDefinitions('stub_table')
        );
    }

    /**
     * @dataProvider validNameProvider
     */
    public function testBasicField($fieldName)
    {
        $field = $this->createFieldInstance($fieldName);

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
     * @expectedException \Nemo64\Typo3Api\Exception\TcaFieldException
     * @dataProvider invalidNameProvider
     * @param mixed $name
     */
    public function testInvalidName($name)
    {
        $this->createFieldInstance($name);
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
        $field = $this->createFieldInstance($name);
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
        $field = $this->createFieldInstance($fieldName, ['index' => true]);

        $this->assertBasicCtrlChange($field);
        $this->assertBasicColumns($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertEquals(
            [
                'some_table' => [
                    "`$fieldName` " . static::STUB_DB_TYPE,
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
        $field = $this->createFieldInstance($fieldName, ['exclude' => false]);
        $this->assertArrayNotHasKey('exclude', $field->getColumns('stb_table')[$fieldName]);
        $this->assertBasicCtrlChange($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);

        $field = $this->createFieldInstance($fieldName, ['exclude' => true]);
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
        $field = $this->createFieldInstance($fieldName, ['localize' => true]);
        $this->assertBasicCtrlChange($field);
        $this->assertArrayNotHasKey('l10n_mode', $field->getColumns('stb_table')[$fieldName]);
        $this->assertArrayNotHasKey('l10n_display', $field->getColumns('stb_table')[$fieldName]);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);

        $field = $this->createFieldInstance($fieldName, ['localize' => false]);
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
        $field = $this->createFieldInstance($fieldName, ['displayCond' => 'some condition']);
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
        $field = $this->createFieldInstance($fieldName, ['useAsLabel' => true]);

        $ctrl = [];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals($fieldName, $ctrl['label']);
        $this->assertBasicColumns($field);
        $this->assertBasicPalette($field);
        $this->assertBasicShowItem($field);
        $this->assertBasicDatabase($field);

        $ctrl = ['label' => 'other_field'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals('other_field', $ctrl['label']);
        $this->assertEquals($fieldName, $ctrl['label_alt']);

        $ctrl = ['label' => 'other_field_1', 'label_alt' => 'other_field_2'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals('other_field_1', $ctrl['label']);
        $this->assertEquals('other_field_2, ' . $fieldName, $ctrl['label_alt']);
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testSearchField(string $fieldName)
    {
        $field = $this->createFieldInstance($fieldName, ['searchField' => false]);
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertArrayNotHasKey('search_field', $ctrl);

        $field = $this->createFieldInstance($fieldName, ['searchField' => true]);
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals($fieldName, $ctrl['searchFields']);

        $field = $this->createFieldInstance($fieldName, ['searchField' => true]);
        $ctrl = ['searchFields' => 'other_field'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals('other_field, ' . $fieldName, $ctrl['searchFields']);
    }

    /**
     * @dataProvider validNameProvider
     * @param string $fieldName
     */
    public function testLabel(string $fieldName)
    {
        $field = $this->createFieldInstance($fieldName, ['useAsLabel' => false]);
        $ctrl = ['label' => 'uid'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals('uid', $ctrl['label']);

        $field = $this->createFieldInstance($fieldName, ['useAsLabel' => true]);
        $ctrl = ['label' => 'uid'];
        $field->modifyCtrl($ctrl, 'some_table');
        $this->assertEquals($fieldName, $ctrl['label']);
    }
}
