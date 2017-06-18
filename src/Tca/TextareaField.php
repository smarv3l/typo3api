<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 12.06.17
 * Time: 09:55
 */

namespace Typo3Api\Tca;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaField extends TcaField
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'maxLength' => 500,
            'cols' => 30,
            'rows' => function (Options $options) {
                $realCols = $options['cols'] * 2; // cols doesn't cut it exactly so adjust a bit.
                $calculatedSpace = floor($options['maxLength'] / $realCols);
                return min((int)$calculatedSpace, 10);
            },
            'required' => false,
            'trim' => true,
            'eval' => null,
            'dbType' => function (Options $options) {
                $maxLength = $options['maxLength'];
                if ($maxLength < 1 << 10) {
                    // text types don't have a default value and therefor default to null
                    // because of this, i make the varchar version also default to null for consistent behavior
                    return "VARCHAR($maxLength) DEFAULT NULL";
                }

                if ($maxLength < 1 << 16) {
                    return "TEXT DEFAULT NULL";
                }

                return "MEDIUMTEXT DEFAULT NULL";
            },
            // overwrite default exclude default depending on required option
            'exclude' => function (Options $options) {
                return $options['required'] === false;
            },
        ]);

        $resolver->setAllowedTypes('maxLength', 'int');
        $resolver->setAllowedTypes('cols', 'int');
        $resolver->setAllowedTypes('rows', 'int');
        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('trim', 'bool');
        $resolver->setAllowedTypes('eval', ['string', 'null']);

        $resolver->setNormalizer('maxLength', function (Options $options, $maxLength) {

            if ($maxLength < 1) {
                $msg = "Max size of input can't be smaller than 1, got $maxLength";
                throw new InvalidOptionsException($msg);
            }

            if ($maxLength >= 1 << 24) {
                $msg = "The max size of an input field must not be higher than 2^24-1.";
                $msg .= " More characters can't reliably be handled.";
                $msg .= " However, even 2^24-1 chars might fail to localize so use something sensibel.";
                throw new InvalidOptionsException($msg);
            }

            return $maxLength;
        });
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'text',
            'max' => $this->getOption('maxLength'),
            'rows' => $this->getOption('rows'),
            'eval' => implode(',', array_filter([
                $this->getOption('trim') ? 'trim' : null,
                $this->getOption('required') ? 'required' : null,
                $this->getOption('eval')
            ])),
        ];
    }
}