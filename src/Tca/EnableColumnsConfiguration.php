<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 23:02
 */

namespace Typo3Api\Tca;


class EnableColumnsConfiguration implements TcaConfiguration, DefaultTab
{
    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        if (!isset($ctrl['enablecolumns'])) {
            $ctrl['enablecolumns'] = [];
        }
        $ctrl['enablecolumns']['disabled'] = 'hidden';
        $ctrl['enablecolumns']['starttime'] = 'starttime';
        $ctrl['enablecolumns']['endtime'] = 'endtime';
        $ctrl['enablecolumns']['fe_group'] = 'fe_group';

        $ctrl['editlock'] = 'editlock';
    }

    public function getColumns(string $tableName): array
    {
        return [
            'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
            'starttime' => $GLOBALS['TCA']['tt_content']['columns']['starttime'],
            'endtime' => $GLOBALS['TCA']['tt_content']['columns']['endtime'],
            'fe_group' => $GLOBALS['TCA']['tt_content']['columns']['fe_group'],
            'editlock' => $GLOBALS['TCA']['tt_content']['columns']['editlock'],
        ];
    }

    public function getPalettes(string $tableName): array
    {
        return [
            'hidden' => [
                'showitem' => implode(', ', [
                    'hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility'
                ])
            ],
            'access' => [
                'showitem' => implode(', ', [
                    'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
                    'endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
                    '--linebreak--',
                    'fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel',
                    '--linebreak--',
                    'editlock',
                ])
            ],
        ];
    }

    public function getShowItemString(string $tableName): string
    {
        return implode(', ', [
            '--palette--;;hidden',
            '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access'
        ]);
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [
            $tableName => [
                "hidden tinyint(1) DEFAULT '0' NOT NULL",
                "starttime int(11) unsigned DEFAULT '0' NOT NULL",
                "endtime int(11) unsigned DEFAULT '0' NOT NULL",
                "fe_group varchar(100) DEFAULT '0' NOT NULL",
                "editlock tinyint(1) DEFAULT '0' NOT NULL"
            ]
        ];
    }

    public function getDefaultTab(): string
    {
        return 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access';
    }
}