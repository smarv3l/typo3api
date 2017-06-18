<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 11.06.17
 * Time: 20:03
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('items');
        $resolver->setAllowedTypes('items', 'array');

        $resolver->setDefaults([
            // values is just a list of possible values
            // you probably don't need to define it
            'values' => function (Options $options) {
                $values = array_column($options['items'], 1);
                $values = array_filter($values, function ($value) {
                    return $value !== '--div--';
                });
                return $values;
            },

            'required' => true,
            'exclude' => function (Options $options) {
                return $options['required'] === false;
            },

            'dbType' => function (Options $options) {
                $possibleValues = $options['values'];
                $defaultValue = addslashes(reset($possibleValues));
                $maxLength = max(array_map('strlen', $possibleValues));
                return "VARCHAR($maxLength) DEFAULT '$defaultValue' NOT NULL";
            },

            // it doesn't make sense to localize selects most of the time
            'localize' => false
        ]);

        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('values', 'array');

        $resolver->setNormalizer('items', function (Options $options, $items) {
            // ensure at least one value, or an empty value if not required
            if (empty($items) || $options['required'] === false) {
                array_unshift($items, [['', '']]);
            }

            return $items;
        });

        $resolver->setNormalizer('values', function (Options $options, $values) {
            $maxLength = max(array_map('strlen', $values));
            if ($maxLength > 255) {
                throw new InvalidOptionsException("The value in an select shouldn't be longer than 255 bytes.");
            }

            foreach ($values as $value) {
                if (preg_match('/[|,;]/', $value)) {
                    throw new InvalidOptionsException("The value in an select must not contain the chars '|,;'.");
                }
            }

            return $values;
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
}