<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 03.07.17
 * Time: 09:06
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Typo3Api\Builder\TableBuilder;


class SelectRelationField extends TcaField
{
    const ORDER_BY_REGEX = '/(\s*)(ORDER BY(.*))?$/i';

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired([
            'foreign_table'
        ]);

        $resolver->setDefaults([
            'foreign_table_where' => '',
            'required' => false,
            'items' => [],
            'dbType' => "INT(11) DEFAULT '0' NOT NULL",
            'localize' => false,
        ]);

        $resolver->setAllowedTypes('foreign_table', ['string', TableBuilder::class]);
        $resolver->setAllowedTypes('foreign_table_where', 'string');
        $resolver->setAllowedTypes('items', 'array');

        $resolver->setNormalizer('foreign_table', function (Options $options, $foreignTable) {
            if ($foreignTable instanceof TableBuilder) {
                return $foreignTable->getTableName();
            }

            return $foreignTable;
        });

        $resolver->setNormalizer('foreign_table_where', function (Options $options, string $where) {
            $foreignTable = $GLOBALS['TCA'][$options['foreign_table']];

            // append sys_language_uid if available
            if (isset($foreignTable['ctrl']['languageField'])) {
                $languageField = $options['foreign_table'] . '.' . $foreignTable['ctrl']['languageField'];
                $where = preg_replace(self::ORDER_BY_REGEX, "\\1AND $languageField IN (0, -1) \\2", $where, 1);
            }

            // append sorting if available
            if (isset($foreignTable['ctrl']['sortby'])) {
                $sortByField = $options['foreign_table'] . '.' . $foreignTable['ctrl']['sortby'];
                $where = preg_replace_callback(self::ORDER_BY_REGEX, function ($match) use ($sortByField) {
                    if ($match[3]) {
                        return $match[1] . 'ORDER BY' . $match[3] . ', ' . $sortByField;
                    }

                    return $match[1] . 'ORDER BY ' . $sortByField;
                }, $where, 1);
            }

            // append default_sortby if available
            if (isset($foreignTable['ctrl']['default_sortby'])) {
                $sortByDefinitions = GeneralUtility::trimExplode(',', $foreignTable['ctrl']['default_sortby']);
                foreach ($sortByDefinitions as &$sortByDefinition) {
                    $sortByDefinition = $options['foreign_table'] . '.' . $sortByDefinition;
                }

                $sortByStr = implode(', ', $sortByDefinitions);
                $where = preg_replace_callback(self::ORDER_BY_REGEX, function ($match) use ($sortByStr) {
                    if ($match[3]) {
                        return $match[1] . 'ORDER BY' . $match[3] . ', ' . $sortByStr;
                    }

                    return $match[1] . 'ORDER BY ' . $sortByStr;
                }, $where, 1);
            }

            return $where;
        });

        $resolver->setNormalizer('items', function (Options $options, array $items) {
            // ensure at least one value, or an empty value if not required
            if (empty($items) || $options['required'] === false) {
                array_unshift($items, ['', '0']);
            }

            foreach ($items as $item) {
                $value = $item[1];
                if (!preg_match('/^\d+$/', $value)) {
                    $msg = "SelectRelationField options may only be numeric, got '$value'.";
                    throw new InvalidOptionsException($msg);
                }
            }

            return $items;
        });
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => $this->getOption('foreign_table'),
            'foreign_table_where' => $this->getOption('foreign_table_where'),
            'items' => $this->getOption('items'),
            'minitems' => $this->getOption('required') ? 1 : 0,
        ];
    }
}