<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 20:16
 */

namespace Typo3Api\Tca;


class LanguageConfiguration implements TcaConfiguration
{
    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        $ctrl['languageField'] = 'sys_language_uid';
        $ctrl['translationSource'] = 'l10n_source';
        $ctrl['transOrigPointerField'] = 'l18n_parent';
        $ctrl['transOrigDiffSourceField'] = 'l18n_diffsource';
    }

    public function getColumns(string $tableName): array
    {
        return [
            'sys_language_uid' => $GLOBALS['TCA']['tt_content']['columns']['sys_language_uid'],
            'l10n_source' => $GLOBALS['TCA']['tt_content']['columns']['l10n_source'],
            'l18n_diffsource' => $GLOBALS['TCA']['tt_content']['columns']['l18n_diffsource'],
            'l18n_parent' => $GLOBALS['TCA']['tt_content']['columns']['l18n_parent'],
        ];
    }

    public function getPalettes(string $tableName): array
    {
        return [];
    }

    public function getShowItemString(string $tableName): string
    {
        return "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource";
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return [
            $tableName => [
                "sys_language_uid int(11) DEFAULT '0' NOT NULL",
                "l10n_source int(11) DEFAULT '0' NOT NULL",
                "l18n_diffsource mediumtext",
                "l18n_parent int(11) DEFAULT '0' NOT NULL",
            ]
        ];
    }
}