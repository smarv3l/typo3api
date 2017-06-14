<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 21:03
 */

namespace Mp\MpTypo3Api\Hook;


use Mp\MpTypo3Api\Tca\TcaConfiguration;

class SqlSchemaHook
{
    /**
     * @var array
     */
    private static $tableConfigurations = [];

    public static function addTableConfiguration(string $tableName, TcaConfiguration $configuration)
    {
        if (!isset(self::$tableConfigurations[$tableName])) {
            self::$tableConfigurations[$tableName] = [];
        }

        self::$tableConfigurations[$tableName][] = $configuration;
    }

    public function modifyTablesDefinitionString(array $sqlStrings)
    {
        $map = [];

        foreach (self::$tableConfigurations as $tableName => $tableConfiguration) {
            /** @var TcaConfiguration $configuration */
            foreach ($tableConfiguration as $configuration) {
                $tableDefinitions = $configuration->getDbTableDefinitions($tableName);
                foreach ($tableDefinitions as $table => $fieldDefinitions) {
                    if (!isset($map[$table])) {
                        $map[$table] = [];
                    }

                    foreach ($fieldDefinitions as $fieldDefinition) {
                        $map[$table][] = $fieldDefinition;
                    }
                }
            }
        }

        foreach ($map as $tableName => $definitions) {
            // pid is required by tca configuration
            array_unshift($definitions, "pid INT(11) NOT NULL DEFAULT '0'");

            array_unshift($definitions, "uid int(11) NOT NULL auto_increment");
            array_push($definitions, "PRIMARY KEY (uid)");

            $sqlStrings[] = "CREATE TABLE `$tableName` (" . implode(",\n", $definitions) . ");";
        }

        return [$sqlStrings];
    }
}