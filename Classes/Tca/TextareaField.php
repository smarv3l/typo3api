<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 12.06.17
 * Time: 09:55
 */

namespace Mp\MpTypo3Api\Tca;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaField extends TcaField
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'maxLength' => 900,
            'cols' => 30,
            'rows' => function (Options $options) {
                $calculatedSpace = floor($options['maxLength'] / $options['cols']);
                return min($calculatedSpace, 5);
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
                $msg .= " More characters can't reliably be handled.";
                throw new InvalidOptionsException($msg);
            }

            return $maxLength;
        });

        // overwrite default exclude default depending on required option
        $resolver->setDefault('exclude', function (Options $options) {
            return $options['required'] == false;
        });
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'text',
            'max' => $this->getOption('maxLength'),
            'rows' => $this->getOption('rows'),
            'default' => $this->getOption('defaultValue'),
            'eval' => implode(',', array_filter('is_string', [
                $this->getOption('trim') ? 'trim' : null,
                $this->getOption('required') ? 'required' : null,
                $this->getOption('eval')
            ])),
        ];
    }

    public function getDbFieldDefinition(): string
    {
        $maxLength = $this->getOption('maxLength');
        if ($maxLength < 1 << 8) {
            return "VARCHAR($maxLength) DEFAULT '' NOT NULL";
        }

        if ($maxLength < 1 << 16) {
            return "TEXT";
        }

        return "MEDIUMTEXT";
    }
}