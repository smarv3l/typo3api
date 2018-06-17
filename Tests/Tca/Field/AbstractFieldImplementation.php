<?php

namespace Nemo64\Typo3Api\Tca\Field;


use Nemo64\Typo3Api\Builder\Context\TcaBuilderContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractFieldImplementation extends AbstractField
{
    public function getFieldTcaConfig(TcaBuilderContext $tcaContext)
    {
        return [];
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'dbType' => AbstractFieldTest::STUB_DB_TYPE
        ]);
    }

}