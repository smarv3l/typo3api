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
            // that's why i use 50 as a default
            'max' => 50,
            'size' => function (Options $options) {
                return (int)($options['max'] / 2);
            },
            'default' => '',
            'required' => false,
            'trim' => true,
            'charset' => null,
            'is_in' => null,
            'case' => 'any',
            'nospace' => false,

            'dbType' => function (Options $options) {
                $maxLength = $options['max'];
                $default = addslashes($options['default']);
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

        $resolver->setAllowedTypes('max', 'int');
        $resolver->setAllowedTypes('size', 'int');
        $resolver->setAllowedTypes('default', 'string');
        $resolver->setAllowedTypes('required', 'bool');
        $resolver->setAllowedTypes('trim', 'bool');
        $resolver->setAllowedValues('charset', [null, 'alpha', 'alphanum', 'alphanum_x']);
        $resolver->setAllowedTypes('is_in', ['null', 'string']);
        $resolver->setAllowedValues('case', ['lower', 'upper', 'any']);
        $resolver->setAllowedTypes('nospace', 'bool');

        $resolver->setNormalizer('max', function (Options $options, $maxLength) {

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
            'size' => $this->getOption('size'),
            'max' => $this->getOption('max'),
            'default' => $this->getOption('default'),
            'is_in' => $this->getOption('is_in'),
            'eval' => implode(',', $this->getEvals()),
        ];
    }

    /**
     * @return array
     */
    protected function getEvals()
    {
        $evals = [];

        if ($this->getOption('trim')) {
            $evals[] = 'trim';
        }

        if ($this->getOption('required')) {
            $evals[] = 'required';
        }

        if ($this->getOption('charset')) {
            $evals[] = $this->getOption('charset');
        }

        if ($this->getOption('is_in')) {
            $evals[] = 'is_in';
        }

        if ($this->getOption('case') !== 'any') {
            $evals[] = $this->getOption('case');
        }

        if ($this->getOption('nospace')) {
            $evals[] = 'nospace';
        }

        return $evals;
    }
}