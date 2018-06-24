<?php

namespace Typo3Api\Tca;

use PHPUnit\Framework\TestCase;
use Typo3Api\Builder\TableBuilder;
use Typo3Api\Hook\SqlSchemaHookUtil;
use Typo3Api\PreparationForTypo3;

class EnableColumnsConfigurationTest extends TestCase
{
    use PreparationForTypo3;
    use SqlSchemaHookUtil;

    public function testConfigure()
    {
        TableBuilder::create('test_table')
            ->configure(new EnableColumnsConfiguration())
        ;

        $sql = [
            "test_table" => array_merge(
                BaseConfigurationTest::BASE_SQL,
                [
                    "hidden tinyint(1) DEFAULT '0' NOT NULL",
                    "starttime int(11) unsigned DEFAULT '0' NOT NULL",
                    "endtime int(11) unsigned DEFAULT '0' NOT NULL",
                    "fe_group varchar(100) DEFAULT '0' NOT NULL",
                    "editlock tinyint(1) DEFAULT '0' NOT NULL",
                ]
            )
        ];

        $this->assertEquals(array_replace_recursive(
            BaseConfigurationTest::BASE_TCA,
            [
                'ctrl' => [
                    'editlock' => 'editlock',
                    'enablecolumns' => [
                        'disabled' => 'hidden',
                        'starttime' => 'starttime',
                        'endtime' => 'endtime',
                        'fe_group' => 'fe_group',
                    ],
                    'EXT' => [
                        'typo3api' => [
                            'sql' => $sql
                        ]
                    ]
                ],
                'columns' => [
                    'hidden' => $GLOBALS['TCA']['tt_content']['columns']['hidden'],
                    'starttime' => $GLOBALS['TCA']['tt_content']['columns']['starttime'],
                    'endtime' => $GLOBALS['TCA']['tt_content']['columns']['endtime'],
                    'fe_group' => $GLOBALS['TCA']['tt_content']['columns']['fe_group'],
                    'editlock' => $GLOBALS['TCA']['tt_content']['columns']['editlock'],
                ],
                'types' => [
                    '1' => [
                        'showitem' => implode(', ', [
                            '--div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
                            '--palette--;;hidden',
                            '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access',
                        ]),
                    ],
                ],
                'palettes' => [
                    'hidden' => [
                        'showitem' => implode(', ', [
                            'hidden;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.visibility'
                        ]),
                    ],
                    'access' => [
                        'showitem' => implode(', ', [
                            'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
                            'endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
                            '--linebreak--',
                            'fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:fe_group_formlabel',
                            '--linebreak--',
                            'editlock',
                        ]),
                    ],
                ],
            ]
        ), $GLOBALS['TCA']['test_table']);

        $this->assertSqlInserted($sql);
    }
}
