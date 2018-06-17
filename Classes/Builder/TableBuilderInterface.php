<?php


namespace Nemo64\Typo3Api\Builder;


interface TableBuilderInterface extends TcaBuilderInterface
{
    public function getTableName(): string;
}