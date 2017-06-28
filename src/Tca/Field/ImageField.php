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
            'minitems' => 0,
            'maxitems' => 100,
            'allowHide' => function (Options $options) {
                // if you define minitems, you'd expect there to be at least one item.
                // however: hiding elements will prevent this so i just decided to disable hiding by default then.
                return $options['minitems'] === 0;
            },
            'dbType' => function (Options $options) {
                return DbFieldDefinition::getIntForNumberRange(0, $options['maxitems']);
            },
            'exclude' => function (Options $options) {
                return $options['minitems'] === 0;
            },
        ]);
        
        $resolver->setAllowedTypes('minitems', 'int');
        $resolver->setAllowedTypes('maxitems', 'int');
        $resolver->setAllowedTypes('allowHide', 'bool');

        $resolver->setNormalizer('minitems', function (Options $options, $minitems) {
            if ($minitems < 0) {
                throw new InvalidOptionsException("minitems must not be smaller than 0");
            }

            return $minitems;
        });

        $resolver->setNormalizer('maxitems', function (Options $options, $maxitems) {
            if ($maxitems < $options['minitems']) {
                throw new InvalidOptionsException("maxitems must not be smaller than minitems");
            }

            return $minitems;
        });
    }

    public function getFieldTcaConfig(string $tableName)
    {
        $config = $GLOBALS['TCA']['tt_content']['columns']['image']['config'];
        $config['foreign_match_fields']['fieldname'] = $this->getOption('name');
        $config['minitems'] = $this->getOption('minitems');
        $config['maxitems'] = $this->getOption('maxitems');
        return $config;
    }
}