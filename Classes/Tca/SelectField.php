<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 11.06.17
 * Time: 20:03
 */

namespace Mp\MpTypo3Api\Tca;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectField extends TcaField
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('items');
        $resolver->setAllowedTypes('items', 'array');
        $resolver->setNormalizer('items', function (Options $options, $value) {
            // ensure at least one value
            if (empty($value)) {
                $value = [['', '']];
            }

            return $value;
        });

        $resolver->setAllowedTypes('values', 'array');
        $resolver->setDefault('values', function (Options $options) {
            $values = array_column($options['items'], 1);
            $values = array_filter($values, function ($value) {
                return $value !== '--div--';
            });
        });
        $resolver->setNormalizer('values', function (Options $options, $values) {
            $maxLength = max(array_map('strlen', $values));
            if ($maxLength > 255) {
                throw new InvalidOptionsException("The value in an select shouldn't be longer than 255 bytes.");
            }

            foreach ($values as $value) {
                if (preg_match('/[|,;]/', $value)) {
                    throw new InvalidOptionsException("The valine in an select must not contain the chars '|,;'.");
                }
            }

            return $values;
        });

        $resolver->setAllowedTypes('is_int', 'bool');
        $resolver->setDefault('is_int', function (Options $options) {
            foreach ($options['values'] as $value) {
                if (!preg_match('/^[0-9]+$/', $value)) {
                    return false;
                }
            }

            return true;
        });

        // overwrite default exclude default depending on required option
        $resolver->setDefault('exclude', function (Options $options) {
            return $options['required'] == false;
        });
    }

    public function getFieldTcaConfig(string $tableName)
    {
        return [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => $this->getOption('items'),
        ];
    }

    public function getDbFieldDefinition(): string
    {
        $possibleValues = $this->getOption('values');
        $defaultValue = addslashes(reset($possibleValues));

        if ($this->getOption('is_int')) {
            $intValues = array_map('intval', $possibleValues);
            $minValue = min($intValues);
            $maxValue = max($intValues);
            $strLength = max(array_map('strlen', [$minValue, $maxValue]));
            $maxAbsValue = max(array_map('abs', [$minValue, $maxValue]));

            if ($minValue >= 0) {
                if ($maxAbsValue < 1 << 8) {
                    return "TINYINT($strLength) UNSIGNED DEFAULT '$defaultValue' NOT NULL";
                }

                if ($maxAbsValue < 1 << 16) {
                    return "SMALLINT($strLength) UNSIGNED DEFAULT '$defaultValue' NOT NULL";
                }

                if ($maxAbsValue < 1 << 24) {
                    return "MEDIUMINT($strLength) UNSIGNED DEFAULT '$defaultValue' NOT NULL";
                }

                if ($maxAbsValue < 1 << 32) {
                    return "INT($strLength) UNSIGNED DEFAULT '$defaultValue' NOT NULL";
                }
                // don't use BIGINT because of the difficulties in php handling 64 bit numbers... rather use string
            } else {

                // FIXME this calculation isnt completly right
                if ($maxAbsValue < 1 << 7) {
                    return "TINYINT($strLength) DEFAULT '$defaultValue' NOT NULL";
                }

                if ($maxAbsValue < 1 << 15) {
                    return "SMALLINT($strLength) DEFAULT '$defaultValue' NOT NULL";
                }

                if ($maxAbsValue < 1 << 23) {
                    return "MEDIUMINT($strLength) DEFAULT '$defaultValue' NOT NULL";
                }

                if ($maxAbsValue < 1 << 31) {
                    return "INT($strLength) DEFAULT '$defaultValue' NOT NULL";
                }
            }
        }

        $maxFieldSize = max(array_map('strlen', $possibleValues));
        $minFieldSize = min(array_map('strlen', $possibleValues));
        if ($minFieldSize === $maxFieldSize) {
            return "CHAR($maxFieldSize) DEFAULT '$defaultValue' NOT NULL";
        }

        return "VARCHAR($maxFieldSize) DEFAULT '$defaultValue' NOT NULL";
    }
}