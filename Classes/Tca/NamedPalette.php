<?php

namespace Typo3Api\Tca;


class NamedPalette extends CompoundTcaConfiguration
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, array $children = [])
    {
        if (preg_match('/[,;]/', $name)){
            throw new \RuntimeException("The name of a palette must not contain comma or semicolon, got $name");
        }

        parent::__construct($children);
        $this->name = $name;
    }

    public function getPaletteName(string $tableName)
    {
        return preg_replace('/\W+/', '_', parent::getShowItemString($tableName));
    }

    public function getPalettes(string $tableName): array
    {
        $showItems = [];

        foreach ($this->children as $child) {
            $showItems[] = $child->getShowItemString($tableName);
        }

        $palettes = [
            $this->getPaletteName($tableName) => [
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
        return "--palette--; {$this->name}; " . $this->getPaletteName($tableName);
    }
}