<?php

namespace Nemo64\Typo3Api\Tca;

use PHPUnit\Framework\TestCase;
use Nemo64\Typo3Api\Builder\TableBuilder;
use Nemo64\Typo3Api\Hook\SqlSchemaHookUtil;
use Nemo64\Typo3Api\PreparationForTypo3;
use Nemo64\Typo3Api\Tca\Field\InputField;

class CompoundTcaConfigurationTest extends TestCase
{
    use PreparationForTypo3;
    use SqlSchemaHookUtil;

    /**
     * @param TcaConfigurationInterface[] $instances
     * @return CompoundTcaConfiguration
     */
    protected function createInstance(TcaConfigurationInterface ...$instances): CompoundTcaConfiguration
    {
        return new CompoundTcaConfiguration($instances);
    }

    public function testAddingTwoFields()
    {
        $field1 = new InputField('field_1');
        $field2 = new InputField('field_2');

        $tableBuilder = TableBuilder::createFullyNamed('test_table');
        $tableBuilder->configure($this->createInstance($field1, $field2));

        $this->assertEquals(
            $field1->getColumns('test_table')['field_1'],
            $GLOBALS['TCA']['test_table']['columns']['field_1']
        );
        $this->assertEquals(
            $field2->getColumns('test_table')['field_2'],
            $GLOBALS['TCA']['test_table']['columns']['field_2']
        );
        $this->assertSqlInserted([
            'test_table' => array_replace_recursive(BaseConfigurationTest::BASE_SQL, [
                'field_1' => "`field_1` VARCHAR(50) DEFAULT '' NOT NULL",
                'field_2' => "`field_2` VARCHAR(50) DEFAULT '' NOT NULL",
            ])
        ]);
    }

    public function testMergeTwoFields()
    {
        $field1 = new InputField('field_1');
        $field2 = new InputField('field_2');

        $tableBuilder = TableBuilder::createFullyNamed('test_table', 'alt_type');
        $tableBuilder->configure($field1);

        $tableBuilder = TableBuilder::createFullyNamed('test_table');
        $tableBuilder->configure($this->createInstance($field1, $field2));

        $this->assertEquals(
            $field1->getColumns('test_table')['field_1'],
            $GLOBALS['TCA']['test_table']['columns']['field_1']
        );
        $this->assertEquals(
            $field2->getColumns('test_table')['field_2'],
            $GLOBALS['TCA']['test_table']['columns']['field_2']
        );
        $this->assertEquals(
            ['showitem' => '--div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, field_1'],
            $GLOBALS['TCA']['test_table']['types']['alt_type']
        );
        $this->assertSqlInserted([
            'test_table' => array_replace_recursive(BaseConfigurationTest::BASE_SQL, [
                'field_1' => "`field_1` VARCHAR(50) DEFAULT '' NOT NULL",
                'field_2' => "`field_2` VARCHAR(50) DEFAULT '' NOT NULL",
            ])
        ]);
    }
}
