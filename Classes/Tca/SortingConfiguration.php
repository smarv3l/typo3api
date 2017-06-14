<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 22:02
 */

namespace Mp\MpTypo3Api\Tca;


class SortingConfiguration implements TcaConfiguration
{
    public function modifyTca(array &$tca, string $tableName)
    {
        $tca['ctrl']['sortby'] = 'sorting';
        $tca['columns']['sorting'] = [
            'config' => ['type' => 'passthrough']
        ];
    }

    public function getShowItemString(): string
    {
        return '';
    }

    public function getDbTableDefinitions($tableName): array
    {
        return [$tableName => ["sorting int(11) DEFAULT '0' NOT NULL"]];
    }
}