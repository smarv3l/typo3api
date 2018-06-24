<?php

namespace Typo3Api\Tca;


use Typo3Api\Builder\Context\TcaBuilderContext;


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

    public function getPalettes(TcaBuilderContext $tcaBuilder): array
    {
        $showItems = [];

        foreach ($this->children as $child) {
            $showItems[] = $child->getShowItemString($tcaBuilder);
        }

        $palettes = [
            $this->name => [
                'showitem' => implode(', ', array_filter($showItems))
            ]
        ];

        foreach ($this->children as $child) {
            foreach ($child->getPalettes($tcaBuilder) as $paletteName => $paletteDefinition) {
                $palettes[$paletteName] = $paletteDefinition;
            }
        }

        return $palettes;
    }

    public function getShowItemString(TcaBuilderContext $tcaBuilder): string
    {
        return '--palette--;; ' . $this->name;
    }
}