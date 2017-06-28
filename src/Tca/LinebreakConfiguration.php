<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 28.06.17
 * Time: 13:30
 */

namespace Typo3Api\Tca;


class LinebreakConfiguration implements TcaConfiguration
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