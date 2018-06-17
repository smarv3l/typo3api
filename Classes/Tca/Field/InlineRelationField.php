<?php

namespace Nemo64\Typo3Api\Tca\Field;


use Nemo64\Typo3Api\Builder\Context\TcaBuilderContext;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Nemo64\Typo3Api\Builder\Context\TableBuilderContext;
use Nemo64\Typo3Api\Utility\DbFieldDefinition;

class InlineRelationField extends AbstractField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('foreign_table');
        $resolver->setDefaults([
            'foreign_field' => 'parent_uid',
            // if foreignTakeover is true, the other table is exclusive for this relation (recommended)
            // this means hideTable will be set to true, and some other behaviors will change
            // however: you can still use the foreign table for other inline relations
            'foreignTakeover' => true,
            'minitems' => 0,
            'maxitems' => 100, // at some point, inline record editing doesn't make sense anymore
            'collapseAll' => function (Options $options) {
                return $options['maxitems'] > 5;
            },

            'dbType' => function (Options $options) {
                return DbFieldDefinition::getIntForNumberRange(0, $options['maxitems']);
            },
        ]);

        $resolver->setAllowedTypes('foreign_table', ['string', TableBuilderContext::class]);
        $resolver->setAllowedTypes('foreign_field', 'string');
        $resolver->setAllowedTypes('minitems', 'int');
        $resolver->setAllowedTypes('maxitems', 'int');

        /** @noinspection PhpUnusedParameterInspection */
        $resolver->setNormalizer('foreign_table', function (Options $options, $foreignTable) {
            if ($foreignTable instanceof TableBuilderContext) {
                return $foreignTable->getTableName();
            }

            return $foreignTable;
        });

        $resolver->setNormalizer('minitems', function (Options $options, $minItems) {
            if ($minItems < 0) {
                throw new InvalidOptionsException("Minitems can't be smaller than 0, got $minItems.");
            }

            if (
                $minItems > 0
                && isset($GLOBALS['TCA'][$options['foreign_table']]['ctrl']['enablecolumns'])
                && !empty($GLOBALS['TCA'][$options['foreign_table']]['ctrl']['enablecolumns'])
            ) {
                $msg = "minitems can't be used if the foreign_table has enablecolumns. This is to prevent unexpected behavior.";
                $msg .= " Someone could create a relation and disable the related record (eg. by setting endtime).";
                $msg .= " Typo3 can't catch that so it is better to just not use minitems in combination with enablecolumns.";
                throw new InvalidOptionsException($msg);
            }

            return $minItems;
        });
    }

    public function getFieldTcaConfig(TcaBuilderContext $tcaBuilder)
    {
        $foreignTable = $this->getOption('foreign_table');
        if (!isset($GLOBALS['TCA'][$foreignTable])) {
            throw new \RuntimeException("Configure $foreignTable before adding it in the irre configuraiton of $tableName");
        }

        $foreignTableDefinition = $GLOBALS['TCA'][$foreignTable];
        $sortby = @$foreignTableDefinition['ctrl']['sortby'] ?: @$foreignTableDefinition['ctrl']['_sortby'];
        $canBeSorted = (bool)$sortby;
        $canLocalize = (bool)@$foreignTableDefinition['ctrl']['languageField'];
        $canHide = (bool)@$foreignTableDefinition['columns']['hidden'];

        // this is the takeover part... it will modify globals which isn't so nice
        // TODO create a better spot to modify globals.. this doesn't fit here
        if ($this->getOption('foreignTakeover')) {
            // the doc states that sortby should be disabled if the table is exclusive for this relation
            // https://docs.typo3.org/typo3cms/TCAReference/8-dev/ColumnsConfig/Type/Inline.html#foreign-sortby
            if ($sortby) {
                $GLOBALS['TCA'][$foreignTable]['ctrl']['sortby'] = null;
                $GLOBALS['TCA'][$foreignTable]['ctrl']['sortby_'] = $sortby;
            }

            // ensure only this relation sees the other table
            $GLOBALS['TCA'][$foreignTable]['ctrl']['hideTable'] = true;

            // since this table can't normally be created anymore, remove creation restrictions
            ExtensionManagementUtility::allowTableOnStandardPages($foreignTable);
        }

        return [
            'type' => 'inline',
            'foreign_table' => $this->getOption('foreign_table'),
            'foreign_field' => $this->getOption('foreign_field'),
            'foreign_sortby' => $sortby,
            'minitems' => $this->getOption('minitems'),
            'maxitems' => $this->getOption('maxitems'),
            'behaviour' => [
                'enableCascadingDelete' => $this->getOption('foreignTakeover'),
                'localizeChildrenAtParentLocalization' => $canLocalize
            ],
            'appearance' => [
                'collapseAll' => $this->getOption('collapseAll') ? 1 : 0,
                'useSortable' => $canBeSorted,
                'showPossibleLocalizationRecords' => $canLocalize,
                'showRemovedLocalizationRecords' => $canLocalize,
                'showAllLocalizationLink' => $canLocalize,
                'showSynchronizationLink' => $canLocalize, // potentially dangerous...
                'enabledControls' => [
                    'info' => TRUE,
                    'new' => TRUE,
                    'dragdrop' => $canBeSorted,
                    'sort' => $canBeSorted,
                    'hide' => $canHide,
                    'delete' => TRUE,
                    'localize' => $canLocalize,
                ],
            ],
        ];
    }

    public function getColumns(TcaBuilderContext $tcaBuilder): array
    {
        $columns = parent::getColumns($tcaBuilder);

        if ($this->getOption('localize') === false) {
            // remove the l10n display options
            // inline field cant be displayed as readonly
            unset($columns[$this->getOption('name')]['l10n_display']);
        }

        return $columns;
    }

    public function getDbTableDefinitions(TableBuilderContext $tableBuilder): array
    {
        $tableDefinitions = parent::getDbTableDefinitions($tableBuilder);

        // define the field on the other side
        // TODO somewhere it should be checked if this field is already defined
        $foreignField = addslashes($this->getOption('foreign_field'));
        $tableDefinitions[$this->getOption('foreign_table')] = [
            "`$foreignField` INT(11) DEFAULT '0' NOT NULL",
            "KEY `$foreignField`(`$foreignField`)"
        ];

        return $tableDefinitions;
    }
}