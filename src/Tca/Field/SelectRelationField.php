<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 03.07.17
 * Time: 09:06
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectRelationField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired([
            'foreign_table'
        ]);

        $resolver->setDefaults([
            'foreign_table_where' => '', // todo default to sorting ~ maybe even normalize
            'required' => false,
            'items' => [],
            'dbType' => "INT(11) DEFAULT '0' NOT NULL",
            'localize' => false,
        ]);

        $resolver->setNormalizer('foreign_table_where', function (Options $options, string $where) {

            // append sys_language_uid if available
            $foreignTable = $GLOBALS['TCA'][$options['foreign_table']];
            if (isset($foreignTable['ctrl']['languageField'])) {
                $languageField = $options['foreign_table'] . '.' . $foreignTable['ctrl']['languageField'];
                $where = preg_replace('/ ORDER BY|$/i', " AND $languageField IN (0, -1)\0", $where);
            }

            return $where;
        });

        $resolver->setNormalizer('items', function (Options $options, array $items) {
            if ($options['required'] === false) {
                array_unshift($items, ['', '0']);
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