<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 14.07.17
 * Time: 23:45
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'type' => 'date',
            'min' => -1 << 31,
            'max' => 1 << 31 - 1,

            /** @see \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::getPlainValue */
            'useDateTime' => true,
            'dbType' => function (Options $options) {
                if ($options['useDateTime']) {
                    switch ($options['type']) {
                        case 'date':
                            return "DATE DEFAULT NULL";
                        case 'datetime':
                            return "DATETIME DEFAULT NULL";
                        // other formats are atm not supported by typo3
                    }
                }

                return "INT(11) DEFAULT NULL";
            },
            'localize' => false,
        ]);

        $resolver->setAllowedValues('type', ['date', 'datetime', 'time', 'timesec']);
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'input',
            'renderType' => 'inputDateTime',
            'dbType' => $this->getOption('useDateTime') ? 'datetime' : null,
            'eval' => $this->getOption('type'),
            'range' => [
                'lower' => $this->getOption('min'),
                'upper' => $this->getOption('max'),
            ],
        ];
    }
}