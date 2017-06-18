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
        ]);

        $resolver->setNormalizer('maxLength', function (Options $options, $maxLength) {
            if ($maxLength < 1) {
                $msg = "Max size of input can't be smaller than 1, got $maxLength";
                throw new InvalidOptionsException($msg);
            }

            if ($maxLength >= 1 << 24) {
                $msg = "The max size of an input field must not be higher than 2^24-1.";
                $msg -= " More characters can't reliably be handled.";
                throw new InvalidOptionsException($msg);
            }

            return $maxLength;
        });

        // overwrite default exclude default depending on required option
        $resolver->setDefault('exclude', function (Options $options) {
            return $options['required'] == false;
        });
    }

    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        parent::modifyCtrl($ctrl, $tableName);

        if (!isset($ctrl['label'])) {
            $ctrl['label'] = $this->getName();
        } else {
            if (!isset($ctrl['label_alt'])) {
                $ctrl['label_alt'] = $this->getName();
            } else {
                $ctrl['label_alt'] .= ', ' . $this->getName();
            }
        }

        if (!isset($ctrl['searchFields'])) {
            $ctrl['searchFields'] = $this->getName();
        } else {
            $ctrl['searchFields'] .= ', ' . $this->getName();
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

    public function getDbFieldDefinition(): string
    {
        $maxChars = $this->getOption('maxLength');

        if ($maxChars < 1 << 8) {
            $default = addslashes($this->getOption('defaultValue'));
            return "VARCHAR($maxChars) DEFAULT '$default' NOT NULL";
        }

        if ($maxChars < 1 << 16) {
            return "TEXT";
        }

        if ($maxChars < 1 << 24) {
            return "MEDIUMTEXT";
        }

        // i ignore longtext as texts these long will create multiple problems
        throw new \RuntimeException("Max size error ~ should have been caught by setMaxChars");
    }
}