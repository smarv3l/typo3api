<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 01.07.17
 * Time: 23:25
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\OptionsResolver;

class EmailField extends InputField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'max' => 100,
            'localize' => false
        ]);
    }

    protected function getEvals()
    {
        $evals = parent::getEvals();
        $evals[] = 'email';
        return $evals;
    }

}