<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 02.07.17
 * Time: 22:30
 */

namespace Typo3Api\Tca;


use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomConfiguration implements TcaConfigurationInterface
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'ctrl' => [],
            'columns' => [],
            'palettes' => [],
            'showitem' => '',
            'dbTableDefinition' => []
        ]);

        $resolver->setAllowedTypes('ctrl', 'array');
        $resolver->setAllowedTypes('columns', 'array');
        $resolver->setAllowedTypes('palettes', 'array');
        $resolver->setAllowedTypes('showitem', ['array', 'string']);
        $resolver->setAllowedTypes('dbTableDefinition', 'array');

        /** @noinspection PhpUnusedParameterInspection */
        $resolver->setNormalizer('showitem', function (Options $options, $showitem) {
            if (is_array($showitem)) {
                $showitem = implode(', ', array_filter($showitem));
            }

            return $showitem;
        });

        $this->options = $resolver->resolve($options);
    }

    public function getOption(string $option)
    {
        return $this->options[$option];
    }

    public function modifyCtrl(array &$ctrl, string $tableName)
    {
        foreach ($this->getOption('ctrl') as $key => $value) {
            $ctrl[$key] = $value;
        }
    }

    public function getColumns(string $tableName): array
    {
        return $this->getOption('columns');
    }

    public function getPalettes(string $tableName): array
    {
        return $this->getOption('palettes');
    }

    public function getShowItemString(string $tableName): string
    {
        return $this->getOption('showitem');
    }

    public function getDbTableDefinitions(string $tableName): array
    {
        return $this->getOption('dbTableDefinition');
    }
}