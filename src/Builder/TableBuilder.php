<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:51
 */

namespace Typo3Api\Builder;


use Typo3Api\Hook\SqlSchemaHook;
use Typo3Api\Tca\TcaConfiguration;

class TableBuilder
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * TableBuilder constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->tableName = $name;
        $this->configureTableIfNotPresent();
        SqlSchemaHook::attach();
    }

    public static function create(string $extkey, string $name)
    {
        $extPrefix = 'tx_' . str_replace('_', '', $extkey) . '_';
        $tableBuilder = new static($extPrefix . $name);
        $tableBuilder->setTitle(ucwords(str_replace('_', ' ', $name)));
        return $tableBuilder;
    }

    public static function createForModel(string $extkey, string $name)
    {
        $extPrefix = 'tx_' . str_replace('_', '', $extkey) . '_';
        $modelName = 'domain_model_' . str_replace('_', '', $name);
        $tableBuilder = new static($extPrefix . $modelName);
        $tableBuilder->setTitle(ucwords(str_replace('_', ' ', $name)));
        return $tableBuilder;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configure(TcaConfiguration $configuration)
    {
        $tableName = $this->getTableName();

        $configuration->modifyCtrl($GLOBALS['TCA'][$tableName]['ctrl'], $tableName);

        $columns = $configuration->getColumns($tableName);
        foreach ($columns as $key => $value) {
            $GLOBALS['TCA'][$tableName]['columns'][$key] = $value;
        }

        $palettes = $configuration->getPalettes($tableName);
        foreach ($palettes as $key => $palette) {
            $GLOBALS['TCA'][$tableName]['palettes'][$key] = $palette;
        }

        $showItemString = $configuration->getShowItemString($tableName);
        if (strlen($showItemString) > 0) {
            if (!isset($GLOBALS['TCA'][$tableName]['types']['1']['showitem'])) {
                $GLOBALS['TCA'][$tableName]['types']['1']['showitem'] = $showItemString;
            } else {
                $GLOBALS['TCA'][$tableName]['types']['1']['showitem'] .= ', ' . $showItemString;
            }
        }

        SqlSchemaHook::addTableConfiguration($tableName, $configuration);

        return $this;
    }

    public function getTitle(): string
    {
        if (!is_string($GLOBALS['TCA'][$this->getTableName()]['ctrl']['title'])) {
            return '';
        }

        return $GLOBALS['TCA'][$this->getTableName()]['ctrl']['title'];
    }

    public function setTitle(string $title)
    {
        $GLOBALS['TCA'][$this->getTableName()]['ctrl']['title'] = $title;
    }

    /**
     * @return bool
     */
    protected function configureTableIfNotPresent(): bool
    {
        if (isset($GLOBALS['TCA'][$this->getTableName()])) {
            return false;
        }

        $GLOBALS['TCA'][$this->getTableName()] = [
            'ctrl' => [
                'dividers2tabs' => true,
                'title' => $this->getTableName(),
            ],
            'interface' => [
                'showRecordFieldList' => ''
            ],
            'columns' => [],
            'types' => [],
            'palettes' => [],
        ];

        return true;
    }
}