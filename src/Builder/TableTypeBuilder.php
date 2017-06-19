<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 20:13
 */

namespace Typo3Api\Builder;


use Typo3Api\Hook\SqlSchemaHook;
use Typo3Api\Tca\TcaConfiguration;

class TableTypeBuilder
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
     * TableBuilder constructor.
     * @param string $tableName
     * @param string $typeName
     * @internal param string $name
     */
    public function __construct(string $tableName, string $typeName)
    {
        $this->tableName = $tableName;
        $this->typeName = $typeName;
    }

    public static function create(string $tableName, string $typeName)
    {
        return new static($tableName, $typeName);
    }

    /**
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configure(TcaConfiguration $configuration)
    {
        $tca =& $GLOBALS['TCA'][$this->getTableName()];

        if (!isset($tca['types'][$this->getTypeName()])) {
            $tca['types'][$this->getTypeName()] = [];
        }
        $typeDefinition =& $tca['types'][$this->getTypeName()];

        $showItemString = $configuration->getShowItemString($this->getTableName());
        if (isset($typeDefinition['showitem'])) {
            $typeDefinition['showitem'] .= ', ' . $showItemString;
        } else {
            $typeDefinition['showitem'] = $showItemString;
        }

        foreach ($configuration->getPalettes($this->getTableName()) as $paletteName => $paletteDefinition) {
            // TODO i assume the palette hasn't changed
            if (isset($tca['palettes'][$paletteName])) {
                continue;
            }

            $tca['palettes'][$paletteName] = $paletteDefinition;
        }

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
            // TODO detect which overwrites are nessesary... maybe making overwrites optional
        }

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function inheritConfigurationFrom(string $type)
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
}