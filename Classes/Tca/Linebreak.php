<?php

namespace Nemo64\Typo3Api\Tca;


class Linebreak implements TcaConfigurationInterface
{
    public function modifyCtrl(array &$ctrl, string $tableName)
    {
    }

    public function getColumns(string $tableName): array
    {
        return [];
    }

    public function getPalettes(string $tableName): array
    {
        return [];
    }

    public function getShowItemString(string $tableName): string
    {
        return '--linebreak--';
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [];
    }

}