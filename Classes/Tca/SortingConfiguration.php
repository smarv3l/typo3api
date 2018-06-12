<?php

namespace Nemo64\Typo3Api\Tca;


class SortingConfiguration implements TcaConfigurationInterface
{
    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        $ctrl['sortby'] = 'sorting';
    }

    public function getColumns(string $tableName): array
    {
        return [
            'sorting' => [
                'config' => [
                    'type' => 'passthrough'
                ]
            ]
        ];
    }

    public function getPalettes(string $tableName): array
    {
        return [];
    }

    public function getShowItemString(string $tableName): string
    {
        return '';
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [
            $tableName => [
                "sorting int(11) DEFAULT '0' NOT NULL",

                // sorting is always local to the pid so putting that in the index might help a lot
                "INDEX sorting (pid, sorting ASC)",
            ]
        ];
    }
}