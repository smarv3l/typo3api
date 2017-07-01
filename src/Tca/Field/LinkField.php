<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 01.07.17
 * Time: 19:21
 */

namespace Typo3Api\Tca\Field;


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

    public function getFieldTcaConfig(string $tableName)
    {
        $config = parent::getFieldTcaConfig($tableName);
        $config['renderType'] = 'inputLink';
        $config['softref'] = 'typolink';
        return $config;
    }
}