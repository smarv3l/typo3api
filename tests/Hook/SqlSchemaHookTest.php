<?php

namespace Typo3Api\Hook;

use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Install\Service\SqlExpectedSchemaService;
use Typo3Api\Tca\CustomConfiguration;

class SqlSchemaHookTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        // what? you think you can execute typo3 code with warnings enabled? are you crazy?
        error_reporting(E_ERROR | E_WARNING | E_DEPRECATED);
    }

    public function setUp()
    {
        SqlSchemaHook::reset();
    }

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
        $fieldDefinition = '`title` VARCHAR(32) DEFAULT "" NOT NULL';
        SqlSchemaHook::addTableConfiguration('test_table', new CustomConfiguration([
            'dbTableDefinition' => ['test_table' => [$fieldDefinition]]
        ]));

        $schemaHook = new SqlSchemaHook();
        $sql = $schemaHook->modifyTablesDefinitionString([]);
        $this->assertEquals([["CREATE TABLE `test_table` (\n$fieldDefinition\n);"]], $sql);
    }

    public function testModifyTable()
    {
        $previousDefinition = "CREATE TABLE `test_table` (uid int(11) NOT NULL auto_increment, PRIMARY KEY (uid));";
        $fieldDefinition = '`title` VARCHAR(32) DEFAULT "" NOT NULL';
        SqlSchemaHook::addTableConfiguration('test_table', new CustomConfiguration([
            'dbTableDefinition' => ['test_table' => [$fieldDefinition]]
        ]));

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
