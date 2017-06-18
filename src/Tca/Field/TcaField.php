<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:19
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Typo3Api\Tca\TcaConfiguration;

abstract class TcaField implements TcaConfiguration
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $options;

    /**
     * CommonField constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct(string $name, array $options = [])
    {
        $this->name = $name;

        $optionResolver = new OptionsResolver();
        $this->configureOptions($optionResolver);
        $this->options = $optionResolver->resolve($options);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'dbType'
        ]);
        $resolver->setDefaults([
            'label' => function (Options $options) {
                $splitName = preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $this->getName());
                return ucfirst(trim(strtolower($splitName)));
            },
            'exclude' => false,
            'localize' => true,
            'displayCond' => null,
        ]);

        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('exclude', 'bool');
        $resolver->setAllowedTypes('dbType', 'string');
        $resolver->setAllowedTypes('localize', 'bool');
        $resolver->setAllowedTypes('displayCond', ['string', 'null']);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getOption(string $name)
    {
        return $this->options[$name];
    }

    public function modifyCtrl(array &$ctrl, string $tableName)
    {
    }

    public function getColumns(string $tableName): array
    {
        return [
            $this->getName() => [
                'label' => $this->getOption('label'),
                'exclude' => $this->getOption('exclude'),
                'config' => $this->getFieldTcaConfig($tableName),
                'l10n_mode' => $this->getOption('localize') ? '' : 'exclude',
                'l10n_display' => $this->getOption('localize') ? '' : 'defaultAsReadonly',
                'displayCond' => $this->getOption('displayCond'),
            ]
        ];
    }

    public function getPalettes(string $tableName): array
    {
        return [];
    }

    abstract public function getFieldTcaConfig(string $tableName);

    public function getDbTableDefinitions(string $tableName): array
    {
        $name = addslashes($this->getName());
        return [
            $tableName => [
                "`$name` " . $this->getOption('dbType')
            ]
        ];
    }

    public function getShowItemString(string $tableName): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}