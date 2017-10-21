<?php


namespace Typo3Api;


use TYPO3\CMS\Core\Utility\GeneralUtility;
use Typo3Api\Hook\SqlSchemaHook;

trait PreparationForTypo3
{
    public static function setUpBeforeClass()
    {
        // what? you think you can execute typo3 code with warnings enabled? are you crazy?
        error_reporting(E_ERROR | E_WARNING | E_DEPRECATED);
    }

    public function setUp()
    {
        // load tt_content tca because it is used as a reference in many configurations
        $tca = require __DIR__ . '/../vendor/typo3/cms/typo3/sysext/frontend/Configuration/TCA/tt_content.php';
        if (is_array($tca)) {
            $GLOBALS['TCA']['tt_content'] = $tca;
        }
    }

    public function tearDown()
    {
        SqlSchemaHook::reset();
        GeneralUtility::purgeInstances();
        unset($GLOBALS['TCA']);
    }
}