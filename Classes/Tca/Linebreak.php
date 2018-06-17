<?php

namespace Nemo64\Typo3Api\Tca;


use Nemo64\Typo3Api\Builder\Context\TableBuilderContext;
use Nemo64\Typo3Api\Builder\Context\TcaBuilderContext;


class Linebreak implements TcaConfigurationInterface
{
    public function modifyCtrl(array &$ctrl, TcaBuilderContext $tcaBuilder)
    {
    }

    public function getColumns(TcaBuilderContext $tcaBuilder): array
    {
        return [];
    }

    public function getPalettes(TcaBuilderContext $tcaBuilder): array
    {
        return [];
    }

    public function getShowItemString(TcaBuilderContext $tcaBuilder): string
    {
        return '--linebreak--';
    }

    public function getDbTableDefinitions(TableBuilderContext $tcaBuilder): array
    {
        return [];
    }

}