<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:15
 */

namespace Typo3Api\Tca;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputField extends TcaField
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'maxLength' => 255,
            'visibleSize' => function (Options $options) {
                return min(30, $options['maxLength']);
            },
            'defaultValue' => '',
            'required' => false,
            'trim' => true,
            'eval' => null,
            'useAsLabel' => true,
            'searchField' => true,

            'dbType' => function (Options $options) {
                $maxLength = $options['maxLength'];
                $default = addslashes($options['defaultValue']);
                return "VARCHAR($maxLength) DEFAULT '$default' NOT NULL";
            },
            // overwrite default exclude default depending on required option
            'exclude' => function (Options $options) {
                return $options['required'] === false;
            },
        ]);

        $resolver->setAllowedTypes('maxLength', 'int');
        $resolver->setAllowedTypes('visibleSize', 'int');
        $resolver->setAllowedTypes('defaultValue', 'string');
        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('trim', 'bool');
        $resolver->setAllowedTypes('eval', ['string', 'null']);
        $resolver->setAllowedTypes('useAsLabel', 'bool');
        $resolver->setAllowedTypes('searchField', 'bool');
        $resolver->setNormalizer('maxLength', function (Options $options, $maxLength) {

            if ($maxLength < 1) {
                $msg = "Max size of input can't be smaller than 1, got $maxLength";
                throw new InvalidOptionsException($msg);
            }

            if ($maxLength > 1024) {
                $msg = "The max size of an input field must not be higher than 1024.";
                throw new InvalidOptionsException($msg);
            }

            return $maxLength;
        });
    }

    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        parent::modifyCtrl($ctrl, $tableName);

        if ($this->getOption('useAsLabel')) {
            if (!isset($ctrl['label'])) {
                $ctrl['label'] = $this->getName();
            } else {
                if (!isset($ctrl['label_alt'])) {
                    $ctrl['label_alt'] = $this->getName();
                } else {
                    $ctrl['label_alt'] .= ', ' . $this->getName();
                }
            }
        }

        if ($this->getOption('searchField')) {
            if (!isset($ctrl['searchFields'])) {
                $ctrl['searchFields'] = $this->getName();
            } else {
                $ctrl['searchFields'] .= ', ' . $this->getName();
            }
        }
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'input',
            'size' => $this->getOption('visibleSize'),
            'max' => $this->getOption('maxLength'),
            'default' => $this->getOption('defaultValue'),
            'eval' => implode(',', array_filter([
                $this->getOption('trim') ? 'trim' : null,
                $this->getOption('required') ? 'required' : null,
                $this->getOption('eval')
            ])),
        ];
    }
}