<?php


namespace Nemo64\Typo3Api\Hook;


use TYPO3\CMS\Core\Utility\GeneralUtility;

trait SqlSchemaHookUtil
{
    public function assertSqlInserted(array $expected, $message = '')
    {
        /** @var SqlSchemaHook $schemaHook */
        $schemaHook = GeneralUtility::makeInstance(SqlSchemaHook::class);
        $sql = $schemaHook->modifyTablesDefinitionString([]);

        $definitions = array_map(function ($tableName, $fieldDefinitions) {
            return "CREATE TABLE `$tableName` (\n" . implode(",\n", $fieldDefinitions) . "\n);";
        }, array_keys($expected), array_values($expected));
        $this->assertEquals([$definitions], $sql, $message);
    }
}