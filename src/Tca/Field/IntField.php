<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 13:41
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Typo3Api\Utility\DbFieldDefinition;

class IntField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'min' => 0,
            'max' => 10000,
            'visibleSize' => function (Options $options) {
                return (int)(max(array_map('strlen', [$options['min'], $options['max']])) / 2);
            },
            'defaultValue' => function (Options $options) {
                if ($options['min'] <= 0 && $options['max'] >= 0) {
                    return 0;
                }

                return $options['min'];
            },
            'required' => false, // TODO required is kind of useless on an int

            'dbType' => function (Options $options) {
                $low = $options['min'];
                $high = $options['max'];
                $default = $options['defaultValue'];
                return DbFieldDefinition::getIntForNumberRange($low, $high, $default);
            },
            // overwrite default exclude default depending on required option
            'exclude' => function (Options $options) {
                return $options['required'] === false;
            },
            // an int field is something which most of the time isn't required to be localized
            'localize' => false,
        ]);

        $resolver->setAllowedTypes('min', 'int');
        $resolver->setAllowedTypes('max', 'int');
        $resolver->setAllowedTypes('visibleSize', 'int');
        $resolver->setAllowedTypes('defaultValue', 'int');
        $resolver->setAllowedTypes('required', 'bool');
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'input',
            'size' => $this->getOption('visibleSize'),
            'default' => $this->getOption('defaultValue'),
            'range' => [
                'lower' => $this->getOption('min'),
                'upper' => $this->getOption('max')
            ],
            'eval' => 'trim,int' . ($this->getOption('required') ? ',required' : '')
        ];
    }
}