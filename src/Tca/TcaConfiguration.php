<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:13
 */

namespace Typo3Api\Tca;


interface TcaConfiguration
{
    public function modifyCtrl(array &$ctrl, string $tableName);

    public function getColumns(string $tableName): array;

    public function getPalettes(string $tableName): array;

    public function getShowItemString(string $tableName): string;

    public function getDbTableDefinitions(string $tableName): array;
}