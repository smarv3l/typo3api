<?php

namespace Nemo64\Typo3Api\Hook;

use Nemo64\Typo3Api\Builder\Context\TableBuilderContext;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Install\Service\SqlExpectedSchemaService;
use Nemo64\Typo3Api\PreparationForTypo3;
use Nemo64\Typo3Api\Tca\CustomConfiguration;

class SqlSchemaHookTest extends TestCase
{
    use PreparationForTypo3;

    public function testAttach()
    {
        /** @var Dispatcher $signalSlotDispatcher */
        $signalSlotDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
        $slots = $signalSlotDispatcher->getSlots(SqlExpectedSchemaService::class, 'tablesDefinitionIsBeingBuilt');
        $this->assertCount(0, $slots, "Expect no slot being defined by default");

        SqlSchemaHook::attach();

        $slots = $signalSlotDispatcher->getSlots(SqlExpectedSchemaService::class, 'tablesDefinitionIsBeingBuilt');
        $this->assertCount(1, $slots, "Slot must be defined");

        SqlSchemaHook::attach();

        $slots = $signalSlotDispatcher->getSlots(SqlExpectedSchemaService::class, 'tablesDefinitionIsBeingBuilt');
        $this->assertCount(1, $slots, "Slot must only be defined once");
    }

    public function testEmptyModify()
    {
        $schemaHook = new SqlSchemaHook();
        $sql = $schemaHook->modifyTablesDefinitionString([]);
        $this->assertEquals([[]], $sql);
    }

    public function testCreateTable()
    {
        $testTable = new TableBuilderContext('test_table', '1');

        $fieldDefinition = '`title` VARCHAR(32) DEFAULT "" NOT NULL';
        $configuration = new CustomConfiguration(['dbTableDefinition' => ['test_table' => [$fieldDefinition]]]);
        $GLOBALS['TCA']['test_table']['ctrl']['EXT']['typo3api']['sql'] = $configuration->getDbTableDefinitions($testTable);

        $schemaHook = new SqlSchemaHook();
        $sql = $schemaHook->modifyTablesDefinitionString([]);
        $this->assertEquals([["CREATE TABLE `test_table` (\n$fieldDefinition\n);"]], $sql);
    }

    public function testModifyTable()
    {
        $testTable = new TableBuilderContext('test_table', '1');

        $previousDefinition = "CREATE TABLE `test_table` (uid int(11) NOT NULL auto_increment, PRIMARY KEY (uid));";
        $fieldDefinition = '`title` VARCHAR(32) DEFAULT "" NOT NULL';
        $configuration = new CustomConfiguration(['dbTableDefinition' => ['test_table' => [$fieldDefinition]]]);
        $GLOBALS['TCA']['test_table']['ctrl']['EXT']['typo3api']['sql'] = $configuration->getDbTableDefinitions($testTable);

        $schemaHook = new SqlSchemaHook();
        $sql = $schemaHook->modifyTablesDefinitionString([$previousDefinition]);
        $this->assertEquals([
            [
                $previousDefinition,
                "CREATE TABLE `test_table` (\n$fieldDefinition\n);"
            ]
        ], $sql);
    }
}
