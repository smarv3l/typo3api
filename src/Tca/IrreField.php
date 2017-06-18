<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 11.06.17
 * Time: 21:57
 */

namespace Typo3Api\Tca;


use Symfony\Component\OptionsResolver\OptionsResolver;

class IrreField extends TcaField
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('foreignTable');
        $resolver->setDefaults([
            'foreignField' => 'parent_uid',
            'maxItems' => 100, // at some point, inline record editing doesn't make sense anymore
            'collapseAll' => true
        ]);
    }

    public function getFieldTcaConfig(string $tableName)
    {
        $foreignTable = $this->getOption('foreignTable');
        if (!isset($GLOBALS['TCA'][$foreignTable])) {
            throw new \RuntimeException("Configure $foreignTable before adding it in the irre configuraiton of $tableName");
        }

        $foreignTableDefinition = $GLOBALS['TCA'][$foreignTable];
        $sortby = $foreignTableDefinition['ctrl']['sortby'];
        $canLocalize = (bool)$foreignTableDefinition['ctrl']['languageField'];
        $canHide = (bool)$foreignTableDefinition['columns']['hidden'];

        return [
            'type' => 'inline',
            'foreign_table' => $this->getOption('foreignTable'),
            'foreign_field' => $this->getOption('foreignField'),
            'foreign_sortby' => $sortby,
            'maxitems' => $this->getOption('maxItems'),
            'behaviour' => ['enableCascadingDelete' => TRUE],
            'appearance' => [
                'collapseAll' => $this->getOption('collapseAll') ? 1 : 0,
                'useSortable' => $sortby === 'sorting',
                'showSynchronizationLink' => $canLocalize,
                'showPossibleLocalizationRecords' => $canLocalize,
                'showAllLocalizationLink' => $canLocalize,
                'enabledControls' => [
                    'info' => TRUE,
                    'new' => TRUE,
                    'dragdrop' => $sortby === 'sorting',
                    'sort' => $sortby === 'sorting',
                    'hide' => $canHide,
                    'delete' => TRUE,
                    'localize' => $canLocalize,
                ],
            ],
        ];
    }

    public function getDbFieldDefinition(): string
    {
        $maxItems = $this->getOption('maxItems');

        if ($maxItems < 1 << 8) {
            return "TINYINT(3) UNSIGNED DEFAULT '0' NOT NULL";
        }

        if ($maxItems < 1 << 16) {
            return "SMALLINT(5) UNSIGNED DEFAULT '0' NOT NULL";
        }

        return "INT(10) UNSIGNED DEFAULT '0' NOT NULL";
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        $tableDefinitions = parent::getDbTableDefinitions($tableName);

        // define the field on the other side
        // TODO somewhere it should be checked if this field is already defined
        $foreignField = addslashes($this->getOption('foreignField'));
        $tableDefinitions[$this->getOption('foreignTable')] = [
            "`$foreignField` INT(11) DEFAULT '0' NOT NULL"
        ];

        return $tableDefinitions;
    }
}