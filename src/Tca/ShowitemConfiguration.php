<?php

namespace Typo3Api\Tca;


/**
 * This simple configuration just passes though the showitem string passed during construct.
 * It can be used to easily reuse fields that have already been defined (for example while creating a new type).
 */
class ShowitemConfiguration implements TcaConfigurationInterface
{
    /**
     * @var string
     */
    private $showitem;

    /**
     * @param string|array $showitem
     */
    public function __construct($showitem)
    {
        if (is_array($showitem)) {
            $showitem = implode(', ', $showitem);
        }

        if (!is_string($showitem)) {
            $type = is_object($showitem) ? get_class($showitem) : gettype($showitem);
            throw new \RuntimeException("Expected showitem to be a string or an array, got $type.");
        }

        $this->showitem = $showitem;
    }

    public function modifyCtrl(array &$ctrl, string $tableName)
    {
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
        return $this->showitem;
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [];
    }
}