<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 21:03
 */

namespace Typo3Api\Hook;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Install\Service\SqlExpectedSchemaService;
use Typo3Api\Tca\TcaConfigurationInterface;

class SqlSchemaHook
{
    /**
     * @var array
     */
    private static $tableConfigurations = [];

    /**
     * @var bool
     */
    private static $eventAttached = false;

    public static function addTableConfiguration(string $tableName, TcaConfigurationInterface $configuration)
    {
        if (!isset(self::$tableConfigurations[$tableName])) {
            self::$tableConfigurations[$tableName] = [];
        }

        self::$tableConfigurations[$tableName][] = $configuration;
    }

    public static function attach()
    {
        if (self::$eventAttached) {
            return;
        }

        /** @var Dispatcher $signalSlotDispatcher */
        $dispatcherClass = Dispatcher::class;
        $signalSlotDispatcher = GeneralUtility::makeInstance($dispatcherClass);
        $signalSlotDispatcher->connect(
            SqlExpectedSchemaService::class,
            'tablesDefinitionIsBeingBuilt',
            SqlSchemaHook::class,
            'modifyTablesDefinitionString'
        );
        self::$eventAttached = true;
    }

    public function modifyTablesDefinitionString(array $sqlStrings)
    {
        $map = [];

        foreach (self::$tableConfigurations as $tableName => $tableConfiguration) {
            /** @var TcaConfigurationInterface $configuration */
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
            $sqlStrings[] = "CREATE TABLE `$tableName` (\n" . implode(",\n", $definitions) . "\n);";
        }

        return [$sqlStrings];
    }
}