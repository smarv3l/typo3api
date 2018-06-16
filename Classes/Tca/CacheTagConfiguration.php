<?php

namespace Nemo64\Typo3Api\Tca;


/**
 * Class CacheTagConfiguration
 *
 * ->configure(mew \Nemo64\Typo3Api\Tca\CacheTagConfiguration('tx_myextension'))
 * ->configure(mew \Nemo64\Typo3Api\Tca\CacheTagConfiguration('tx_myextension_table_####uid###'))
 */
class CacheTagConfiguration implements TcaConfigurationInterface
{
    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $group;

    public function __construct(string $tag, string $group = 'pages')
    {
        $this->tag = $tag;
        $this->group = $group;
    }

    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        $ctrl['EXT']['typo3api']['cache_tags'][$this->group][$this->tag] = $this->tag;
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
        return '';
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [];
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}