<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 20:16
 */

namespace Mp\MpTypo3Api\Tca;


class LanguageConfiguration implements TcaConfiguration
{
    public function modifyTca(array &$tca, string $tableName)
    {
        $tca['ctrl']['languageField'] = 'sys_language_uid';
        $tca['ctrl']['translationSource'] = 'l10n_source';
        $tca['ctrl']['transOrigPointerField'] = 'l18n_parent';
        $tca['ctrl']['transOrigDiffSourceField'] = 'l18n_diffsource';

        $tca['columns']['sys_language_uid'] = $GLOBALS['TCA']['tt_content']['columns']['sys_language_uid'];
        $tca['columns']['l10n_source'] = $GLOBALS['TCA']['tt_content']['columns']['l10n_source'];
        $tca['columns']['l18n_diffsource'] = $GLOBALS['TCA']['tt_content']['columns']['l18n_diffsource'];
        $tca['columns']['l18n_parent'] = $GLOBALS['TCA']['tt_content']['columns']['l18n_parent'];
    }

    public function getShowItemString(): string
    {
        return "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource";
    }

    public function getDbTableDefinitions($tableName): array
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