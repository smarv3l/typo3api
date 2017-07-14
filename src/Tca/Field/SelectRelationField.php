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
use Typo3Api\Utility\ForeignTableUtility;


class SelectRelationField extends TcaField
{
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
            return ForeignTableUtility::normalizeForeignTableWhere($options['foreign_table'], $where);
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
        ];
    }
}