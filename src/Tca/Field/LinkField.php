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

class LinkField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'size' => 50,

            'required' => false,
            'trim' => true,
            'eval' => null,
            'dbType' => "VARCHAR(1024) DEFAULT '' NOT NULL",

            // overwrite default exclude default depending on required option
            'exclude' => function (Options $options) {
                return $options['required'] === false;
            },
            'localize' => false,
        ]);
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'input',
            'renderType' => 'inputLink',
            'size' => $this->getOption('size'),
            'max' => 1024,
            'eval' => implode(',', array_filter([
                $this->getOption('trim') ? 'trim' : null,
                $this->getOption('required') ? 'required' : null,
                $this->getOption('eval')
            ])),
            'softref' => 'typolink'
        ];
    }
}