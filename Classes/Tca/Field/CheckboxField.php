<?php

namespace Nemo64\Typo3Api\Tca\Field;


use Nemo64\Typo3Api\Builder\Context\TcaBuilderContext;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckboxField extends AbstractField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'checkbox_label' => 'Enabled',
            'default' => false,
            'dbType' => function (Options $options) {
                $default = $options['default'] ? '1' : '0';
                return "TINYINT(1) DEFAULT '$default' NOT NULL";
            },
            'localize' => false
        ]);

        $resolver->setAllowedTypes('checkbox_label', 'string');
        $resolver->setAllowedTypes('default', 'bool');
    }

    public function getFieldTcaConfig(TcaBuilderContext $tcaBuilder)
    {
        return [
            'type' => 'check',
            'default' => $this->getOption('default'),
            'items' => [
                [$this->getOption('checkbox_label'), '']
            ]
        ];
    }
}