<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 11.06.17
 * Time: 19:34
 */

namespace Mp\MpTypo3Api\Tca;


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class PageRelationConfiguration implements TcaConfiguration
{
    /**
     * @var bool
     */
    private $restrictToStoragePage;
    /**
     * @var bool
     */
    private $showInListView;

    public function __construct(bool $restrictToStoragePage = true, bool $showInListView = true)
    {
        $this->restrictToStoragePage = $restrictToStoragePage;
        $this->showInListView = $showInListView;
    }

    public function modifyTca(array &$tca, string $tableName)
    {
        $tca['ctrl']['hideTable'] = !$this->showInListView;

        if (!$this->restrictToStoragePage) {
            ExtensionManagementUtility::allowTableOnStandardPages($tableName);
        }
    }

    public function getShowItemString(): string
    {
        return "";
    }

    public function getDbTableDefinitions($tableName): array
    {
        // pid must be defined in any tca table... because typo3
        // therefor this is already defined in the SqlSchemaHook
        return [];
    }
}