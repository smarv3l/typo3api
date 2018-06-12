<?php

namespace Nemo64\Typo3Api\Tca\Field;


class InputFieldTest extends AbstractFieldTest
{
    public function createFieldInstance(string $name, array $options = [], $extendDefaults = true): AbstractField
    {
        if ($extendDefaults) {
            $options = ['dbType' => self::STUB_DB_TYPE] + $options;
        }

        return new InputField($name, $options);
    }

    public function assertBasicCtrlChange(AbstractField $field)
    {
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'stub_table');
        $this->assertEquals([
            'searchFields' => $field->getName(),
            'label' => $field->getName(),
        ], $ctrl);
    }

    protected function assertBasicColumns(AbstractField $field)
    {
        $this->assertEquals([
            $field->getName() => [
                'label' => $field->getOption('label'),
                'config' => [
                    'type' => 'input',
                    'size' => 25,
                    'max' => 50,
                    'eval' => 'trim',
                    'default' => '',
                ]
            ]
        ], $field->getColumns('some_table'));
    }

    public static function differentSizeProvider()
    {
        return [
            [10],
            [50],
            [250],
            [500],
        ];
    }

    /**
     * @dataProvider differentSizeProvider
     * @param int $size
     */
    public function testDifferentSizes(int $size)
    {
        $fieldName = 'test_field_1';
        $field = $this->createFieldInstance($fieldName, ['max' => $size], false);
        $this->assertEquals($size, $field->getColumns('t')[$fieldName]['config']['max']);
        $this->assertEquals($size / 2, $field->getColumns('t')[$fieldName]['config']['size']);
        $this->assertEquals("`$fieldName` VARCHAR($size) DEFAULT '' NOT NULL", $field->getDbTableDefinitions('t')['t'][0]);
    }

    public function testDefault()
    {
        $fieldName = 'some_field';

        $default = 'some default';
        $field = $this->createFieldInstance($fieldName, ['default' => $default], false);
        $this->assertBasicCtrlChange($field);
        $this->assertEquals($default, $field->getColumns('t')[$fieldName]['config']['default']);
        $this->assertEquals("`$fieldName` VARCHAR(50) DEFAULT '$default' NOT NULL", $field->getDbTableDefinitions('t')['t'][0]);

        $field = $this->createFieldInstance($fieldName, ['default' => ''], false);
        $this->assertBasicCtrlChange($field);
        $this->assertEquals('', $field->getColumns('t')[$fieldName]['config']['default']);
        $this->assertEquals("`$fieldName` VARCHAR(50) DEFAULT '' NOT NULL", $field->getDbTableDefinitions('t')['t'][0]);
    }

    public function testPlaceholder()
    {
        $fieldName = 'some_field';

        $field = $this->createFieldInstance($fieldName, ['placeholder' => 'eg. Some Street']);
        $this->assertBasicCtrlChange($field);
        $this->assertEquals('eg. Some Street', $field->getColumns('t')[$fieldName]['config']['placeholder']);

        $field = $this->createFieldInstance($fieldName, ['placeholder' => '']);
        $this->assertBasicCtrlChange($field);
        $this->assertEquals('', $field->getColumns('t')[$fieldName]['config']['placeholder']);

        $field = $this->createFieldInstance($fieldName, ['placeholder' => null]);
        $this->assertBasicCtrlChange($field);
        $this->assertArrayNotHasKey('placeholder', $field->getColumns('t')[$fieldName]['config']);

    }
}
