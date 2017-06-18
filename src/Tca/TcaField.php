<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:19
 */

namespace Typo3Api\Tca;


use Symfony\Component\OptionsResolver\OptionsResolver;

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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => $this->name,
            'exclude' => true,
            'dbType' => "VARCHAR(255) DEFAULT '' NOT NULL",
            'localize' => true,
            'displayCond' => '',
        ]);

        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('exclude', 'bool');
        $resolver->setAllowedTypes('dbType', 'string');
        $resolver->setAllowedTypes('localize', 'bool');
        $resolver->setAllowedTypes('displayCond', 'string');
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
                'label' => $this->options['label'],
                'exclude' => $this->options['exclude'],
                'config' => $this->getFieldTcaConfig($tableName),
                'l10n_mode' => $this->getOption('localize') ? '' : 'exclude',
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