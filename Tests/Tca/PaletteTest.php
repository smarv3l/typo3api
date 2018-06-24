<?php

namespace Typo3Api\Tca;

class PaletteTest extends CompoundTcaConfigurationTest
{
    protected function createInstance(TcaConfigurationInterface ...$instances): CompoundTcaConfiguration
    {
        return new Palette('palette', $instances);
    }

    public function testAddingTwoFields()
    {
        parent::testAddingTwoFields();
        $this->assertEquals(
            ['showitem' => 'field_1, field_2'],
            $GLOBALS['TCA']['test_table']['palettes']['palette']
        );
        $this->assertEquals(
            ['showitem' => '--div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, --palette--;; palette'],
            $GLOBALS['TCA']['test_table']['types']['1']
        );
    }

    public function testMergeTwoFields()
    {
        parent::testMergeTwoFields();
        $this->assertEquals(
            ['showitem' => 'field_1, field_2'],
            $GLOBALS['TCA']['test_table']['palettes']['palette']
        );
        $this->assertEquals(
            ['showitem' => '--div--; LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, --palette--;; palette'],
            $GLOBALS['TCA']['test_table']['types']['1']
        );
    }

}
