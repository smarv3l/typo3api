<?php

namespace Nemo64\Typo3Api\Tca;

use PHPUnit\Framework\TestCase;
use Nemo64\Typo3Api\Builder\TableBuilder;
use Nemo64\Typo3Api\Hook\SqlSchemaHookUtil;
use Nemo64\Typo3Api\PreparationForTypo3;

class BaseConfigurationTest extends TestCase
{
    use PreparationForTypo3;
    use SqlSchemaHookUtil;

    const BASE_TCA = [
        'ctrl' => [
            'delete' => 'deleted',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'origUid' => 'origUid',
            'title' => 'Test table',
            'label' => 'uid'
        ],
        'interface' => [
            'showRecordFieldList' => '',
        ],
        'columns' => [],
        'types' => [
            '1' => []
        ],
        'palettes' => [],
    ];

    const BASE_SQL = [
        "uid int(11) NOT NULL auto_increment",
        "PRIMARY KEY (uid)",
        "pid INT(11) NOT NULL DEFAULT '0'",
        "INDEX pid (pid)",

        "deleted TINYINT(1) DEFAULT '0' NOT NULL",
        "tstamp INT(11) DEFAULT '0' NOT NULL",
        "crdate INT(11) DEFAULT '0' NOT NULL",
        "cruser_id INT(11) DEFAULT '0' NOT NULL",
        "origUid INT(11) DEFAULT '0' NOT NULL",
    ];

    public function testConfiguration()
    {
        TableBuilder::createFullyNamed('test_table');
        // the base configuration is applied automatically

        $this->assertEquals(self::BASE_TCA, $GLOBALS['TCA']['test_table']);
        $this->assertSqlInserted(['test_table' => self::BASE_SQL]);
    }
}
