<?php

namespace Nemo64\Typo3Api\Tca;


interface TcaConfigurationInterface
{
    public function modifyCtrl(array &$ctrl, string $tableName);

    public function getColumns(string $tableName): array;

    public function getPalettes(string $tableName): array;

    public function getShowItemString(string $tableName): string;

    public function getDbTableDefinitions(string $tableName): array;
}