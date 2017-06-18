<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18.06.17
 * Time: 13:01
 */

namespace Typo3Api\Tca\Field;


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
        return ExtensionManagementUtility::getFileFieldTCAConfig(
            $this->getName(),
            [
                'minitems' => $this->getOption('minItems'),
                'maxitems' => $this->getOption('maxItems'),
                'appearance' => [
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