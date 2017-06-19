<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 12.06.17
 * Time: 09:55
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'max' => 500,
            'cols' => 30,
            'rows' => function (Options $options) {
                $realCols = $options['cols'] * 2; // cols doesn't cut it exactly so adjust a bit.
                $calculatedSpace = floor($options['max'] / $realCols);
                return min((int)$calculatedSpace, 10);
            },
            'required' => false,
            'trim' => true,
            'eval' => null,
            'dbType' => function (Options $options) {
                $maxLength = $options['max'];
                if ($maxLength < 1 << 10) {
                    // text types don't have a default value and therefor default to null
                    // because of this, i make the varchar version also default to null for consistent behavior

                    // the reason i don't use tinytext is that in typo3, most of the time, every column is requested
                    // getting a column that is stored outside the table has a performance impact
                    // therefor varchar should generally perform better
                    // however, storing everything in varchar might break the limit of 65535 bytes per row

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

        $resolver->setAllowedTypes('max', 'int');
        $resolver->setAllowedTypes('cols', 'int');
        $resolver->setAllowedTypes('rows', 'int');
        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('trim', 'bool');
        $resolver->setAllowedTypes('eval', ['string', 'null']);

        $resolver->setNormalizer('max', function (Options $options, $maxLength) {

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
            'max' => $this->getOption('max'),
            'rows' => $this->getOption('rows'),
            'eval' => implode(',', array_filter([
                $this->getOption('trim') ? 'trim' : null,
                $this->getOption('required') ? 'required' : null,
                $this->getOption('eval'),
                // i'd love to define null here, but this will render a checkbox which i don't want
            ])),
        ];
    }
}