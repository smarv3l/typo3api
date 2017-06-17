<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:13
 */

namespace Typo3Api\Tca;


interface TcaConfiguration
{
    public function modifyTca(array &$tca, string $tableName);

    public function getShowItemString(): string;

    public function getDbTableDefinitions($tableName): array;
}