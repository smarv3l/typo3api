<?php

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\OptionsResolver;

class AbstractFieldImplementation extends AbstractField
{
    public function getFieldTcaConfig(string $tableName)
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