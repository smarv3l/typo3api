<?php

namespace Nemo64\Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\OptionsResolver;
use Nemo64\Typo3Api\Utility\IntlItemsProcFunc;

class CountryField extends SelectField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('itemsProcFunc', IntlItemsProcFunc::class . '->addCountryNames');
    }
}