<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 10.06.17
 * Time: 19:19
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Typo3Api\Tca\TcaConfigurationInterface;

abstract class AbstractField implements TcaConfigurationInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * A cache for option resolvers to speed up duplicate usage.
     * @var array
     */
    private static $optionResolvers = [];

    /**
     * CommonField constructor.
     * @param string $name
     * @param array $options
     */
    public final function __construct(string $name, array $options = [])
    {
        // Nicer creation syntax when passing name as a direct parameter instead of expecting an option.
        // However: the name must be an option so that it is available during option resolving.
        $options['name'] = $name;

        try {
            $optionResolver = $this->getOptionResolver();
            $this->options = $optionResolver->resolve($options);
        } catch (InvalidOptionsException $e) {
            $msg = "Error while resolving options for the field '$name': " . $e->getMessage();
            throw new InvalidOptionsException($msg, 0, $e);
        }
    }

    private function getOptionResolver()
    {
        if (isset(self::$optionResolvers[get_class($this)])) {
            return self::$optionResolvers[get_class($this)];
        }

        $optionResolver = new OptionsResolver();
        $this->configureOptions($optionResolver);
        self::$optionResolvers[get_class($this)] = $optionResolver;
        return $optionResolver;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'name',
            'dbType'
        ]);
        $resolver->setDefaults([
            'label' => function (Options $options) {
                $splitName = preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $options['name']);
                return ucfirst(trim(strtolower($splitName)));
            },
            'exclude' => false,
            'localize' => true,
            'displayCond' => null,
            'useAsLabel' => false,
            'searchField' => false,
            'useForRecordType' => false,
            'index' => false,
        ]);

        $resolver->setAllowedTypes('name', 'string');
        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('exclude', 'bool');
        $resolver->setAllowedTypes('dbType', 'string');
        $resolver->setAllowedTypes('localize', 'bool');
        $resolver->setAllowedTypes('displayCond', ['string', 'null']);
        $resolver->setAllowedTypes('useAsLabel', 'bool');
        $resolver->setAllowedTypes('searchField', 'bool');
        $resolver->setAllowedTypes('useForRecordType', 'bool');
        $resolver->setAllowedTypes('index', 'bool');

        /** @noinspection PhpUnusedParameterInspection */
        $resolver->setNormalizer('name', function (Options $options, $name) {

            if (strlen($name) > 64) {
                $msg = "The field name should be at most 64 characters long. (and even that... are you insane?)";
                throw new InvalidOptionsException($msg);
            }

            if (strlen($name) <= 0) {
                $msg = "The field name must not be empty";
                throw new InvalidOptionsException($msg);
            }

            if (strtolower($name) !== $name) {
                $msg = "The field name must be lower case.";
                throw new InvalidOptionsException($msg);
            }

            if (!preg_match('#^\w*$#', $name)) {
                $msg = "The field name should only contain word characters to avoid potential problems.";
                throw new InvalidOptionsException($msg);
            }

            return $name;
        });
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
        $fieldName = $this->getOption('name');

        if ($this->getOption('useAsLabel')) {
            if (!isset($ctrl['label'])) {
                $ctrl['label'] = $fieldName;
            } else {
                if (!isset($ctrl['label_alt'])) {
                    $ctrl['label_alt'] = $fieldName;
                } else if (strpos($ctrl['label_alt'], $fieldName) === false) {
                    $ctrl['label_alt'] .= ', ' . $fieldName;
                }
            }
        }

        if ($this->getOption('searchField')) {
            if (!isset($ctrl['searchFields'])) {
                $ctrl['searchFields'] = $fieldName;
            } else if (strpos($ctrl['searchFields'], $fieldName) === false) {
                $ctrl['searchFields'] .= ', ' . $fieldName;
            }
        }

        if ($this->getOption('useForRecordType')) {
            if (isset($ctrl['type'])) {
                $msg = "Only one field can specify the record type for table $tableName.";
                $msg .= " Tried using field " . $fieldName . " as type field.";
                $msg .= " Field " . $ctrl['type'] . " is already defined as type field.";
                throw new \RuntimeException($msg);
            }

            $ctrl['type'] = $fieldName;
        }
    }

    public function getColumns(string $tableName): array
    {
        $column = [
            'label' => $this->getOption('label'),
            'config' => $this->getFieldTcaConfig($tableName),
        ];

        if ($this->getOption('exclude')) {
            $column['exclude'] = true;
        }

        if ($this->getOption('localize') === false) {
            $column['l10n_mode'] = 'exclude';
            $column['l10n_display'] = 'defaultAsReadonly';
        }

        if ($this->getOption('displayCond') !== null) {
            $column['displayCond'] = $this->getOption('displayCond');
        }

        return [
            $this->getOption('name') => $column
        ];
    }

    public function getPalettes(string $tableName): array
    {
        return [];
    }

    abstract public function getFieldTcaConfig(string $tableName);

    public function getDbTableDefinitions(string $tableName): array
    {
        $name = addslashes($this->getOption('name'));
        $definition = [$tableName => ["`$name` " . $this->getOption('dbType')]];

        if ($this->getOption('index')) {
            // TODO I'd really like multi field indexes that are somehow namable
            $definition[$tableName][] = "INDEX `$name`(`$name`)";
        }

        return $definition;
    }

    public function getShowItemString(string $tableName): string
    {
        return $this->getOption('name');
    }
}