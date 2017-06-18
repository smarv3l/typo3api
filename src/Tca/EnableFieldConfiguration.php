<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 23:02
 */

namespace Typo3Api\Tca;


class EnableFieldConfiguration implements TcaConfiguration
{
    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        $ctrl['deleted'] = 'deleted';
        if (!isset($ctrl['enablecolumns'])) {
            $ctrl['enablecolumns'] = [];
        }
        $ctrl['enablecolumns']['disabled'] = 'hidden';
        $ctrl['enablecolumns']['starttime'] = 'starttime';
        $ctrl['enablecolumns']['endtime'] = 'endtime';
    }

    public function getColumns(string $tableName): array
    {
        return [
            'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
            'starttime' => $GLOBALS['TCA']['tt_content']['columns']['starttime'],
            'endtime' => $GLOBALS['TCA']['tt_content']['columns']['endtime'],
        ];
    }

    public function getPalettes(string $tableName): array
    {
        return [
            'access' => [
                'showitem' => implode(', ', [
                    'hidden',
                    'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
                    'endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
                ])
            ]
        ];
    }

    public function getShowItemString(string $tableName): string
    {
        return '--palette--;; access';
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [
            $tableName => [
                "deleted tinyint(1) DEFAULT '0' NOT NULL",
                "hidden tinyint(1) DEFAULT '0' NOT NULL",
                "starttime int(11) unsigned DEFAULT '0' NOT NULL",
                "endtime int(11) unsigned DEFAULT '0' NOT NULL",
            ]
        ];
    }
}