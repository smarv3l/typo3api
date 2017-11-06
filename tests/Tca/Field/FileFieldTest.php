<?php

namespace Typo3Api\Tca\Field;


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class FileFieldTest extends AbstractFieldTest
{
    protected function createFieldInstance(string $name, array $options = []): AbstractField
    {
        return new FileField($name, $options);
    }

    protected function assertBasicColumns(AbstractField $field)
    {
        $this->assertEquals([
            $field->getName() => [
                'label' => $field->getOption('label'),
                'config' => ExtensionManagementUtility::getFileFieldTCAConfig($field->getName(), [
                    'minitems' => 0,
                    'maxitems' => 100,
                    'appearance' => [
                        'collapseAll' => true,
                    ]
                ])
            ]
        ], $field->getColumns('stub_table'));
    }

    /**
     * @param AbstractField $field
     */
    protected function assertBasicDatabase(AbstractField $field)
    {
        $fieldName = $field->getName();
        $this->assertEquals(
            ['stub_table' => ["`$fieldName` TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL"]],
            $field->getDbTableDefinitions('stub_table')
        );
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
                    "`$fieldName` TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL",
                    "INDEX `$fieldName`(`$fieldName`)"
                ]
            ],
            $field->getDbTableDefinitions('some_table')
        );
    }

}