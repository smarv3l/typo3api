<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 13:17
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Typo3Api\Utility\DbFieldDefinition;

class ImageField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'minItems' => 0,
            'maxItems' => 100,
            'allowHide' => function (Options $options) {
                // if you define minItems, you'd expect there to be at least one item.
                // however: hiding elements will prevent this so i just decided to disable hiding by default then.
                return $options['minItems'] === 0;
            },
            'dbType' => function (Options $options) {
                return DbFieldDefinition::getIntForNumberRange(0, $options['maxItems']);
            },
        ]);
    }

    public function getFieldTcaConfig(string $tableName)
    {
        $config = $GLOBALS['TCA']['tt_content']['columns']['image']['config'];
        $config['foreign_match_fields']['fieldname'] = $this->getName();
        return $config;
    }
}