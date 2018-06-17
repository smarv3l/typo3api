<?php

namespace Nemo64\Typo3Api\Tca\Field;


use Nemo64\Typo3Api\Builder\Context\TcaBuilderContext;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkField extends InputField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'size' => 50,
            'max' => 1024,
            'localize' => false,
        ]);
    }

    public function getFieldTcaConfig(TcaBuilderContext $tcaBuilder)
    {
        $config = parent::getFieldTcaConfig($tcaBuilder);
        $config['renderType'] = 'inputLink';
        $config['softref'] = 'typolink';
        return $config;
    }
}