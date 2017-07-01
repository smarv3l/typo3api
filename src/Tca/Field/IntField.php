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
            'size' => function (Options $options) {
                return (int)(max(strlen($options['min']), strlen($options['max'])) / 2);
            },
            'default' => function (Options $options) {
                if ($options['min'] <= 0 && $options['max'] >= 0) {
                    return 0;
                }

                return $options['min'];
            },
            'required' => false, // TODO required is kind of useless on an int since the backend doesn't allow en empty value

            'dbType' => function (Options $options) {
                $low = $options['min'];
                $high = $options['max'];
                $default = $options['default'];
                return DbFieldDefinition::getIntForNumberRange($low, $high, $default);
            },
            // an int field is most of the time not required to be localized
            'localize' => false,
        ]);

        $resolver->setAllowedTypes('min', 'int');
        $resolver->setAllowedTypes('max', 'int');
        $resolver->setAllowedTypes('size', 'int');
        $resolver->setAllowedTypes('default', 'int');
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
            'eval' => 'trim,int' . ($this->getOption('required') ? ',required' : '')
        ];
    }
}