<?php

namespace Typo3Api\Hook;

use PHPUnit\Framework\TestCase;
use Typo3Api\Builder\Context\TableBuilderContext;
use Typo3Api\PreparationForTypo3;
use Typo3Api\Tca\CustomConfiguration;

class SqlSchemaHookTest extends TestCase
{
    use PreparationForTypo3;

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
