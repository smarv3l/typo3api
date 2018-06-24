<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 21:03
 */

namespace Typo3Api\Hook;


use TYPO3\CMS\Core\SingletonInterface;

class SqlSchemaHook implements SingletonInterface
{
    /**
     * The actual hook method.
     *
     * @param array $sqlStrings
     * @return array
     */
    public function modifyTablesDefinitionString(array $sqlStrings)
    {
        $map = [];

        foreach ($GLOBALS['TCA'] as $tableDefinition) {
            if (!isset($tableDefinition['ctrl']['EXT']['typo3api']['sql'])) {
                continue;
            }

            foreach ($tableDefinition['ctrl']['EXT']['typo3api']['sql'] as $table => $fieldDefinitions) {
                if (!isset($map[$table])) {
                    $map[$table] = [];
                }

                foreach ($fieldDefinitions as $fieldDefinition) {
                    $map[$table][] = $fieldDefinition;
                }
            }
        }

        foreach ($map as $tableName => $definitions) {
            $sqlStrings[] = "CREATE TABLE `$tableName` (\n" . implode(",\n", $definitions) . "\n);";
        }

        return [$sqlStrings];
    }
}