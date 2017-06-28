<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 13:01
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Typo3Api\Utility\DbFieldDefinition;

class FileField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'allowedFileExtensions' => '',
            'disallowedFileExtensions' => '', // only makes sense if allowedFileExtensions is empty
            'minitems' => 0,
            'maxitems' => 100,
            'collapseAll' => true,
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

            return $maxitems;
        });
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return ExtensionManagementUtility::getFileFieldTCAConfig(
            $this->getOption('name'),
            [
                'minitems' => $this->getOption('minitems'),
                'maxitems' => $this->getOption('maxitems'),
                'appearance' => [
                    'collapseAll' => $this->getOption('collapseAll'),
                    'enabledControls' => [
                        'hide' => $this->getOption('allowHide')
                    ]
                ]
            ],
            $this->getOption('allowedFileExtensions'),
            $this->getOption('disallowedFileExtensions')
        );
    }
}