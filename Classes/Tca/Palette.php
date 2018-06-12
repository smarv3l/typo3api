<?php

namespace Nemo64\Typo3Api\Tca;


/**
 * @deprecated use the NamedPalette instead
 */
class Palette extends CompoundTcaConfiguration
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, array $children)
    {
        $this->name = $name;
        parent::__construct($children);
    }

    public function getPalettes(string $tableName): array
    {
        $showItems = [];

        foreach ($this->children as $child) {
            $showItems[] = $child->getShowItemString($tableName);
        }

        $palettes = [
            $this->name => [
                'showitem' => implode(', ', array_filter($showItems))
            ]
        ];

        foreach ($this->children as $child) {
            foreach ($child->getPalettes($tableName) as $paletteName => $paletteDefinition) {
                $palettes[$paletteName] = $paletteDefinition;
            }
        }

        return $palettes;
    }

    public function getShowItemString(string $tableName): string
    {
        return '--palette--;; ' . $this->name;
    }
}