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
            'size' => function (Options $options) {
                $preDecimalSize = max(strlen((int)$options['min']), strlen((int)$options['max']));
                return (int)(($preDecimalSize + 2) / 2);
            },
            'default' => function (Options $options) {
                if ($options['min'] <= 0.0 && $options['max'] >= 0.0) {
                    return 0.0;
                }

                return $options['min'];
            },
            'required' => false, // TODO required is kind of useless on an int

            'dbType' => function (Options $options) {
                $low = intval($options['min']);
                $high = intval($options['max']);

                $default = number_format($options['default'], 2, '.', '');
                $decimals = 2; // hardcoded because typo3 only offers double2 validation
                $digits = max(strlen(abs($low)), strlen(abs($high))) + $decimals;

                if ($options['min'] < 0) {
                    return "NUMERIC($digits, $decimals) DEFAULT '$default' NOT NULL";
                } else {
                    return "NUMERIC($digits, $decimals) UNSIGNED DEFAULT '$default' NOT NULL";
                }
            },
            // a double field is most of the time not required to be localized
            'localize' => false,
        ]);

        $resolver->setAllowedTypes('min', ['int', 'double']);
        $resolver->setAllowedTypes('max', ['int', 'double']);
        $resolver->setAllowedTypes('size', 'int');
        $resolver->setAllowedTypes('default', ['int', 'double']);
        $resolver->setAllowedTypes('required', 'bool');
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'input',
            'size' => $this->getOption('size'),
            'default' => $this->getOption('default'),
            'range' => [
                'lower' => $this->getOption('min'),
                'upper' => $this->getOption('max')
            ],
            'eval' => 'trim,double2' . ($this->getOption('required') ? ',required' : '')
        ];
    }
}