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

            'dbType' => function (Options $options) {
                $possibleValues = $options['values'];
                $defaultValue = addslashes(reset($possibleValues));
                $maxChars = max(array_map('mb_strlen', $possibleValues));
                return "VARCHAR($maxChars) DEFAULT '$defaultValue' NOT NULL";
            },

            // it doesn't make sense to localize selects most of the time
            'localize' => false
        ]);

        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('values', 'array');

        $resolver->setNormalizer('items', function (Options $options, $items) {
            // ensure at least one value, or an empty value if not required
            if (empty($items) || $options['required'] === false) {
                array_unshift($items, ['', '']);
            }

            return $items;
        });

        $resolver->setNormalizer('values', function (Options $options, $values) {
            foreach ($values as $value) {
                
                // Why 191 characters?
                // Because mysql indexes can only store 767 bytes and I want to enforce a usefull limit.
                // https://mathiasbynens.be/notes/mysql-utf8mb4#column-index-length
                // Why are you reading this anyways? Did you really try to select a value that has more than 30 chars?
                if (mb_strlen($value) > 191) {
                    $msg = "The value in an select shouldn't be longer than 191 characters.";
                    $msg .= " The longtest value has $maxChars characters.";
                    throw new InvalidOptionsException($msg);
                }
                
                // the documentation says these chars are invalid
                // https://docs.typo3.org/typo3cms/TCAReference/ColumnsConfig/Type/Select.html#items
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