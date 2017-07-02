<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 20:13
 */

namespace Typo3Api\Builder;


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Typo3Api\Hook\SqlSchemaHook;
use Typo3Api\Tca\DefaultTab;
use Typo3Api\Tca\TcaConfiguration;

class TableBuilder
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $typeName;

    /**
     * This is a list of default tabs.
     * Default tabs are passed by the DefaultTab Interface.
     * They are forced to be always at the end.
     *
     * @var array
     */
    private $defaultTabs = [];

    /**
     * TableBuilder constructor.
     * @param string $tableName
     * @param string $typeName
     * @internal param string $name
     */
    public function __construct(string $tableName, string $typeName)
    {
        $this->tableName = $tableName;
        $this->typeName = $typeName;
        SqlSchemaHook::attach();
        $this->configureTableIfNotPresent();
    }

    public static function create(string $extkey, string $name)
    {
        $extPrefix = 'tx_' . str_replace('_', '', $extkey) . '_';
        $tableBuilder = new static($extPrefix . $name, '1');
        $tableBuilder->setTitle(ucfirst(str_replace('_', ' ', $name)));
        return $tableBuilder;
    }

    public static function createForType(string $extkey, string $name, string $typeName)
    {
        $extPrefix = 'tx_' . str_replace('_', '', $extkey) . '_';
        $tableBuilder = new static($extPrefix . $name, $typeName);
        return $tableBuilder;
    }

    /**
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configure(TcaConfiguration $configuration)
    {
        if ($configuration instanceof DefaultTab) {
            $tabName = $configuration->getDefaultTab();
            $this->defaultTabs[] = $tabName;
            return $this->configureInTab($tabName, $configuration);
        } else {
            $tabName = 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general';
            return $this->configureInTab($tabName, $configuration);
        }
    }

    /**
     * @param string $tab
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configureInTab(string $tab, TcaConfiguration $configuration)
    {
        $tca =& $GLOBALS['TCA'][$this->getTableName()];

        $configuration->modifyCtrl($tca['ctrl'], $this->getTableName());
        $this->addShowItemToTab($tca, $configuration, $tab);
        $this->addPalettes($tca, $configuration);
        $this->addColumns($tca, $configuration);

        return $this;
    }

    public function configureAtPosition($position, TcaConfiguration $configuration)
    {
        $tca =& $GLOBALS['TCA'][$this->getTableName()];

        $configuration->modifyCtrl($tca['ctrl'], $this->getTableName());
        $this->addShowItemAtPositon($tca, $configuration, $position);
        $this->addPalettes($tca, $configuration);
        $this->addColumns($tca, $configuration);

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function inheritConfigurationFromType(string $type)
    {
        $tca =& $GLOBALS['TCA'][$this->getTableName()];

        if (!isset($tca['types'][$type])) {
            $msg = "The Type $type isn't defined so it can't be inherited from it.";
            $msg .= " Possible types are: " . implode(', ', array_keys($tca['types']));
            throw new \RuntimeException($msg);
        }

        // TODO maybe not overwrite existing configuration?
        $tca['types'][$this->getTypeName()] = $tca['types'][$type];

        return $this;
    }

    /**
     * @param string $tab
     * @param string $otherTab
     * @return $this
     */
    public function addOrMoveTabInFrontOfTab(string $tab, string $otherTab)
    {
        $type =& $GLOBALS['TCA'][$this->getTableName()]['types'][$this->getTypeName()];

        $search = '/--div--\s*;\s*' . preg_quote($tab, '/') . '.*?(?=,\s?--div--|$)/';
        $match = preg_match($search, $type['showitem'], $results);
        $newTab = $match ? $results[0] : '--div--; ' . $tab;

        if ($match) {
            $type['showitem'] = preg_replace($search, '', $type['showitem'], 1);
        }

        // search the other tab and add the new one in front of it
        $search = '/--div--\s*;\s*' . preg_quote($otherTab, '/') . '.*?(?=,\s?--div--|$)/';
        $type['showitem'] = preg_replace($search, $newTab . ', \0', $type['showitem'], 1, $matches);
        if ($matches === 0) {
            throw new \RuntimeException("The tab '$otherTab' seems to not exist.");
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->typeName;
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
            ],
            'interface' => [
                'showRecordFieldList' => '',
            ],
            'columns' => [],
            'types' => [],
            'palettes' => [],
        ];

        return true;
    }

    /**
     * @param array $tca
     * @param TcaConfiguration $configuration
     */
    protected function addPalettes(array &$tca, TcaConfiguration $configuration)
    {
        $tableName = $this->getTableName();
        $palettes = $configuration->getPalettes($tableName);
        foreach ($palettes as $paletteName => $paletteDefinition) {
            if (isset($tca['palettes'][$paletteName])) {
                if ($paletteDefinition !== $tca['palettes'][$paletteName]) {
                    $msg = "The palette $paletteName is already defined in $tableName but isn't compatible.";
                    $msg .= " If you can rename the palette than that would be an easy fix for the problem.";
                    throw new \RuntimeException($msg);
                }

                continue;
            }

            $tca['palettes'][$paletteName] = $paletteDefinition;
        }
    }

    /**
     * @param array $tca
     * @param TcaConfiguration $configuration
     */
    protected function addColumns(array &$tca, TcaConfiguration $configuration)
    {
        $columns = $configuration->getColumns($this->getTableName());
        $existingColumns = $tca['columns'];
        $missingColumns = array_diff(array_keys($columns), array_keys($existingColumns));

        if (count($missingColumns) === count($columns)) {
            foreach ($columns as $columnName => $columnDefinition) {
                $tca['columns'][$columnName] = $columnDefinition;
            }

            SqlSchemaHook::addTableConfiguration($this->getTableName(), $configuration);
        } else if (count($missingColumns) > 0) {
            throw new \RuntimeException("Partial configuration of a child type is not implemented right now");
        } else {
            // all columns are already defined so...
            // TODO detect which overwrites are nessesary... maybe making overwrites optional somehow
        }
    }

    /**
     * @param array $tca
     * @param TcaConfiguration $configuration
     * @param string $tab
     */
    protected function addShowItemToTab(array &$tca, TcaConfiguration $configuration, string $tab)
    {
        if (!isset($tca['types'][$this->getTypeName()])) {
            $tca['types'][$this->getTypeName()] = [];
        }
        $type =& $tca['types'][$this->getTypeName()];

        $showItemString = $configuration->getShowItemString($this->getTableName());
        if ($showItemString === '') {
            return;
        }

        // search the correct tab and add the content into it
        $search = '/--div--\s*;\s*' . preg_quote($tab, '/') . '.*?(?=,\s?--div--|$)/';
        $type['showitem'] = preg_replace($search, '\0,' . $showItemString, $type['showitem'], 1, $matches);
        if ($matches > 0) {
            return;
        }

        // so the tab did not exist yet...

        $newTab = '--div--; ' . $tab . ', ' . $showItemString;

        // put the new tab right before the first "default tab"
        if (count($this->defaultTabs) > 0 && !in_array($tab, $this->defaultTabs)) {
            $search = '/--div--\s*;\s*' . preg_quote(reset($this->defaultTabs), '/') . '.*?(?=,\s?--div--|$)/';
            $type['showitem'] = preg_replace($search, $newTab . ', \0', $type['showitem'], 1, $matches);
            if ($matches > 0) {
                return;
            }
        }

        // just put the new tab at the end
        $type['showitem'] .= ', ' . $newTab;
    }

    /**
     * @param array $tca
     * @param TcaConfiguration $configuration
     * @param string $position
     */
    protected function addShowItemAtPositon(array &$tca, TcaConfiguration $configuration, string $position)
    {
        if (!isset($tca['types'][$this->getTypeName()])) {
            $tca['types'][$this->getTypeName()] = [];
        }
        $type =& $tca['types'][$this->getTypeName()];

        $showItemString = $configuration->getShowItemString($this->getTableName());
        if ($showItemString === '') {
            return;
        }

        ExtensionManagementUtility::addToAllTCAtypes(
            $this->getTableName(),
            $showItemString,
            $this->getTypeName(),
            $position
        );
    }
}