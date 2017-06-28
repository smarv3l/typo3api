<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 28.06.17
 * Time: 13:03
 */

namespace Typo3Api\Tca;


class PaletteConfiguration implements TcaConfiguration
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var TcaConfiguration[]
     */
    private $children;

    public function __construct(string $name, array $children)
    {
        $this->name = $name;
        $this->children = $children;
    }

    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        foreach ($this->children as $child) {
            $child->modifyCtrl($ctrl, $tableName);
        }
    }

    public function getColumns(string $tableName): array
    {
        $columns = [];

        foreach ($this->children as $child) {
            foreach ($child->getColumns($tableName) as $columnName => $columnDefinition) {
                $columns[$columnName] = $columnDefinition;
            }
        }

        return $columns;
    }

    public function getPalettes(string $tableName): array
    {
        $showItems = [];

        foreach ($this->children as $child) {
            $showItems[] = $child->getShowItemString($tableName);
        }

        $palettes = [
            $this->name => [
                'showitem' => implode(', ', array_filter($showItems))
            ]
        ];

        foreach ($this->children as $child) {
            foreach ($child->getPalettes($tableName) as $paletteName => $paletteDefinition) {
                $palettes[$paletteName] = $paletteDefinition;
            }
        }

        return $palettes;
    }

    public function getShowItemString(string $tableName): string
    {
        return '--palette--;; ' . $this->name;
    }

    public function getDbTableDefinitions(string $tableName): array
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
}