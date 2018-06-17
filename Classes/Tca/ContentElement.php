<?php

namespace Nemo64\Typo3Api\Tca;


use Nemo64\Typo3Api\Builder\Context\TableBuilderContext;
use Nemo64\Typo3Api\Builder\Context\TcaBuilderContext;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ContentElement implements TcaConfigurationInterface
{
    const ICONS = [
        'content-accordion',
        'content-audio',
        'content-bullets',
        'content-carousel',
        'content-coffee', // let's see how long until someone notices
        'content-elements-login',
        'content-elements-mailform',
        'content-elements-searchform',
        'content-form',
        'content-header',
        'content-image',
        'content-media',
        'content-menu-abstract',
        'content-menu-categorized',
        'content-menu-pages',
        'content-menu-recently-updated',
        'content-menu-related',
        'content-menu-section',
        'content-menu-sitemap-pages',
        'content-menu-sitemap',
        'content-menu-thumbnail',
        'content-news',
        'content-panel',
        'content-plugin',
        'content-quote',
        'content-special-div',
        'content-special-html',
        'content-special-menu',
        'content-special-shortcut',
        'content-special-uploads',
        'content-table',
        'content-text-columns',
        'content-text-teaser',
        'content-text',
        'content-textmedia',
        'content-textpic',
    ];

    /**
     * @var array
     */
    private $options;
    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(array $options = [])
    {
        $this->options = $options;

        $this->optionsResolver = new OptionsResolver();
        $this->optionsResolver->setDefaults([
            'name' => function (Options $options) {
                return ucfirst(strtr($options['typeName'], '_', ' '));
            },
            'description' => function (Options $options) {
                return $options['typeName'];
            },
            'icon' => function (Options $options) {
                $icons = array_map(function ($icon) use ($options) {
                    $name = basename($icon, '.svg');
                    return [
                        'diff' => levenshtein($options['typeName'], strtok($name, 'content-')),
                        'name' => $name
                    ];
                }, static::ICONS);
                $icons = array_column($icons, 'name', 'diff');
                ksort($icons);
                \TYPO3\CMS\Core\Utility\DebugUtility::debug($icons);
                return reset($icons);
            },
            'section' => 'common',
        ]);

        // these options will be passed by #getOptions
        $this->optionsResolver->setRequired('typeName');
    }

    private function getOptions(TableBuilderContext $context): array
    {
        return $this->optionsResolver->resolve($this->options + ['typeName' => $context->getTypeName()]);
    }

    public function modifyCtrl(array &$ctrl, TcaBuilderContext $tcaBuilder)
    {
        if (!$tcaBuilder instanceof TableBuilderContext) {
            $type = is_object($tcaBuilder) ? get_class($tcaBuilder) : gettype($tcaBuilder);
            throw new \RuntimeException("Expected " . TableBuilderContext::class . ", got $type");
        }

        if ($tcaBuilder->getTableName() !== 'tt_content') {
            throw new \RuntimeException("Content elements can only be configured for the tt_content table.");
        }

        $options = $this->getOptions($tcaBuilder);
        $ctrl['EXT']['typo3api']['content_elements'][$options['section']][] = [
            'CType' => $options['typeName'],
            'iconIdentifier' => $options['icon'],
            'title' => $options['name'],
            'description' => $options['description'],
            'params' => '&' . http_build_query([
                    'defVals[tt_content]' => [
                        'CType' => $options['typeName'],
                    ],
                ]),
        ];

        // add new type to select choices
        // TODO allow to define a position in dropdown
        $newSelectItem = [$options['name'], $options['typeName'], $options['icon']];
        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = $newSelectItem;
    }

    public function getColumns(TcaBuilderContext $tcaBuilder): array
    {
        return [];
    }

    public function getPalettes(TcaBuilderContext $tcaBuilder): array
    {
        return [];
    }

    public function getShowItemString(TcaBuilderContext $tcaBuilder): string
    {
        return '
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                categories,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                rowDescription,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
        ';
    }

    public function getDbTableDefinitions(TableBuilderContext $tableBuilder): array
    {
        return [];
    }
}