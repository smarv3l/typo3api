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
    public function modifyTca(array &$tca, string $tableName)
    {
        $tca['ctrl']['delete'] = 'deleted';
        $tca['ctrl']['enablecolumns']['disabled'] = 'hidden';
        $tca['ctrl']['enablecolumns']['starttime'] = 'starttime';
        $tca['ctrl']['enablecolumns']['endtime'] = 'endtime';

        $tca['columns']['hidden'] = $GLOBALS['TCA']['tt_content']['columns']['hidden'];
        $tca['columns']['starttime'] = $GLOBALS['TCA']['tt_content']['columns']['starttime'];
        $tca['columns']['endtime'] = $GLOBALS['TCA']['tt_content']['columns']['endtime'];

        $tca['palettes']['access'] = [
            'showitem' => implode(', ', [
                'hidden',
                'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
                'endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            ])
        ];
    }

    public function getShowItemString(): string
    {
        return '--palette--;; access';
    }

    public function getDbTableDefinitions($tableName): array
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