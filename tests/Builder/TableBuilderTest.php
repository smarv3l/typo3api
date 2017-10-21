<?php

namespace Typo3Api\Builder;

use PHPUnit\Framework\TestCase;
use Typo3Api\PreparationForTypo3;

class TableBuilderTest extends TestCase
{
    use PreparationForTypo3;

    public function testCreateTable()
    {
        TableBuilder::createFullyNamed('test_table');
        $this->assertArrayHasKey('ctrl', $GLOBALS['TCA']['test_table']);
        $this->assertArrayHasKey('interface', $GLOBALS['TCA']['test_table']);
        $this->assertArrayHasKey('columns', $GLOBALS['TCA']['test_table']);
        $this->assertArrayHasKey('types', $GLOBALS['TCA']['test_table']);
        $this->assertArrayHasKey('palettes', $GLOBALS['TCA']['test_table']);
    }
}
