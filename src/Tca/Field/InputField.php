<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:15
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputField extends TcaField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            // normally devs put 255 as the default size for inputs because it's the common "best size" for varchar
            // however, in my experience, nobody expects an input to be filled with so much text
            // also: the limit of 255 feels random for a normal human
            // that's why i use 100 as a default
            'maxLength' => 100,
            'visibleSize' => function (Options $options) {
                return min(30, (int) ($options['maxLength'] / 2));
            },
            'defaultValue' => '',
            'required' => false,
            'trim' => true,
            'eval' => null,

            'dbType' => function (Options $options) {
                $maxLength = $options['maxLength'];
                $default = addslashes($options['defaultValue']);
                return "VARCHAR($maxLength) DEFAULT '$default' NOT NULL";
                // using anything but varchar here would make searchFields slow
                // because it doesn't make sense to have a very large input field anyway
                // i opt to prevent large input fields and by default add everything to searchFields
                // also, i can easily use the default option here which is nice.
            },
            // overwrite default exclude default depending on required option
            'exclude' => function (Options $options) {
                return $options['required'] === false;
            },
            'useAsLabel' => true,
            'searchField' => true,
        ]);

        $resolver->setAllowedTypes('maxLength', 'int');
        $resolver->setAllowedTypes('visibleSize', 'int');
        $resolver->setAllowedTypes('defaultValue', 'string');
        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('trim', 'bool');
        $resolver->setAllowedTypes('eval', ['string', 'null']);
        $resolver->setNormalizer('maxLength', function (Options $options, $maxLength) {

            if ($maxLength < 1) {
                $msg = "Max size of input can't be smaller than 1, got $maxLength";
                throw new InvalidOptionsException($msg);
            }

            if ($maxLength > 1024) {
                $msg = "The max size of an input field must not be higher than 1024.";
                $msg .= " Use a textarea instead.";
                throw new InvalidOptionsException($msg);
            }

            return $maxLength;
        });
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