<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 17:20
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Double2Field extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'min' => 0.0,
            'max' => 99.0,
            'visibleSize' => function (Options $options) {
                return (int)(max(array_map('strlen', [$options['min'], $options['max']])) / 2);
            },
            'defaultValue' => function (Options $options) {
                if ($options['min'] <= 0.0 && $options['max'] >= 0.0) {
                    return 0.0;
                }

                return $options['min'];
            },
            'required' => false, // TODO required is kind of useless on an int

            'dbType' => function (Options $options) {
                $low = intval($options['min']);
                $high = intval($options['max']);

                $default = number_format($options['defaultValue'], 2, '.', '');
                $decimals = 2; // hardcoded because typo3 only offers double2 validation
                $digits = max(array_map('strlen', [abs($low), abs($high)])) + $decimals;

                if ($options['min'] < 0) {
                    return "NUMERIC($digits, $decimals) UNSIGNED DEFAULT '$default' NOT NULL";
                } else {
                    return "NUMERIC($digits, $decimals) DEFAULT '$default' NOT NULL";
                }
            },
            // overwrite default exclude default depending on required option
            'exclude' => function (Options $options) {
                return $options['required'] === false;
            },
            // a double field is most of the time not required to be localized
            'localize' => false,
        ]);

        $resolver->setAllowedTypes('min', ['int', 'double']);
        $resolver->setAllowedTypes('max', ['int', 'double']);
        $resolver->setAllowedTypes('visibleSize', 'int');
        $resolver->setAllowedTypes('defaultValue', ['int', 'double']);
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
            'eval' => 'trim,double2' . ($this->getOption('required') ? ',required' : '')
        ];
    }
}