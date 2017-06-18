<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 10:38
 */

namespace Typo3Api\Tca;


class MetaFieldsConfiguration implements TcaConfiguration
{
    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        $ctrl['tstamp'] = 'tstamp';
        $ctrl['crdate'] = 'crdate';
        $ctrl['cruser_id'] = 'cruser_id';
        $ctrl['origUid'] = 'origUid';
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
        return '';
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [$tableName => [
            "tstamp INT(11) DEFAULT '0' NOT NULL",
            "crdate INT(11) DEFAULT '0' NOT NULL",
            "cruser_id INT(11) DEFAULT '0' NOT NULL",
            "origUid INT(11) DEFAULT '0' NOT NULL",
        ]];
    }
}