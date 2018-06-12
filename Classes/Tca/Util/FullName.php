<?php

namespace Typo3Api\Tca\Util;


use Typo3Api\Tca\Field\InputField;
use Typo3Api\Tca\Field\SelectField;
use Typo3Api\Tca\Linebreak;
use Typo3Api\Tca\NamedPalette;

class FullName extends NamedPalette
{
    public function __construct(string $paletteName = 'Name', string $prefix = '', array $options = [])
    {
        if ($prefix !== '') {
            $prefix = rtrim($prefix, '_') . '_';
        }

        parent::__construct($paletteName, $this->getFields($prefix, $options));
    }

    protected function getFields(string $prefix, array $options): array
    {
        return [
            new SelectField($prefix . 'gender', array_replace([
                'label' => 'Salutation',
                'localize' => false,
                'required' => true,
                'items' => [
                    ['Mx', 'x'],
                    ['Mr', 'm'],
                    ['Mrs', 'f'],
                ]
            ], $options['title'] ?? [])),

            new InputField($prefix . 'title', array_replace([
                'label' => 'Title',
                'localize' => false,
                'required' => false,
                'max' => 20
            ], $options['title'] ?? [])),

            new Linebreak(),

            // http://uxmovement.com/forms/why-your-form-only-needs-one-name-field/
            new InputField($prefix . 'name', array_replace([
                'label' => 'Name',
                'localize' => false,
                'required' => true,
            ], $options['first_name'] ?? [])),
        ];
    }
}