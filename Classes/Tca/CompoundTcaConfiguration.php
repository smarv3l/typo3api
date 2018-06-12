<?php

namespace Nemo64\Typo3Api\Tca;
use Traversable;


/**
 * This is a configuration containing multiple configurations.
 * There are some optimizations done in the table builder.
 * Some methods are final for that reason.
 */
class CompoundTcaConfiguration implements TcaConfigurationInterface, \IteratorAggregate
{
    /**
     * This field is protected so extending implementations can access it.
     *
     * @var TcaConfigurationInterface[]
     */
    protected $children;

    public function __construct(array $children = [])
    {
        $this->children = $children;
    }

    /**
     * @return TcaConfigurationInterface[]
     */
    final public function getContainedConfigurations(): array
    {
        return $this->children;
    }

    final public function getIterator(): \Iterator
    {
        foreach ($this->children as $child) {
            yield $child;
        }
    }

    final public function modifyCtrl(array &$ctrl, string $tableName)
    {
        foreach ($this->children as $child) {
            $child->modifyCtrl($ctrl, $tableName);
        }
    }

    final public function getColumns(string $tableName): array
    {
        $columns = [];

        foreach ($this->children as $child) {
            foreach ($child->getColumns($tableName) as $columnName => $columnDefinition) {
                $columns[$columnName] = $columnDefinition;
            }
        }

        return $columns;
    }

    final public function getDbTableDefinitions(string $tableName): array
    {
        $tableDefinitions = [];

        foreach ($this->children as $child) {
            foreach ($child->getDbTableDefinitions($tableName) as $table => $columns) {
                if (!isset($tableDefinitions[$table])) {
                    $tableDefinitions[$table] = $columns;
                } else {
                    foreach ($columns as $column) {
                        $tableDefinitions[$table][] = $column;
                    }
                }
            }
        }

        return $tableDefinitions;
    }

    public function getPalettes(string $tableName): array
    {
        return [];
    }

    public function getShowItemString(string $tableName): string
    {
        return implode(',', array_map(function (TcaConfigurationInterface $configuration) use ($tableName) {
            return $configuration->getShowItemString($tableName);
        }, $this->children));
    }
}