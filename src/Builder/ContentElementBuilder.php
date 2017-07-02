<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 02.07.17
 * Time: 22:12
 */

namespace Typo3Api\Builder;


use Typo3Api\Tca\CustomConfiguration;
use Typo3Api\Tca\ShowitemConfiguration;
use Typo3Api\Tca\TcaConfiguration;

class ContentElementBuilder implements TcaBuilderInterface
{
    /**
     * @var TableBuilder
     */
    private $tableBuilder;

    public function __construct(string $cType)
    {
        $this->tableBuilder = new TableBuilder('tt_content', $cType);

        // add new type to select choices
        $newSelectItem = [$cType, $cType, 'i/tt_content_image.gif'];
        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = $newSelectItem;

        // add basic tt_content configuration
        $this->configureInTab(
            'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
            new ShowitemConfiguration([
                '--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general'
            ])
        );

        // TODO mark the following tabs as default tabs which currently isn't easily possible

        $this->configureInTab(
            'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance',
            new ShowitemConfiguration([
                '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames',
                '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks',
            ])
        );
        $this->configureInTab(
            'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language',
            new ShowitemConfiguration([
                '--palette--;;language'
            ])
        );
        $this->configureInTab(
            'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access',
            new ShowitemConfiguration([
                '--palette--;;hidden',
                '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access'
            ])
        );
        $this->configureInTab(
            'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories',
            new ShowitemConfiguration([
                'categories'
            ])
        );
        $this->configureInTab(
            'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes',
            new ShowitemConfiguration([
                'rowDescription'
            ])
        );
        $this->configureInTab(
            'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extende',
            new ShowitemConfiguration([]) // actually don't configure anything, just add the tab in advance
        );
    }

    /**
     * @param string $cType
     * @return ContentElementBuilder
     */
    public static function create(string $cType): ContentElementBuilder
    {
        return new static($cType);
    }

    /**
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configure(TcaConfiguration $configuration): TcaBuilderInterface
    {
        $this->tableBuilder->configure($configuration);
        return $this;
    }

    /**
     * @param string $tab
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configureInTab(string $tab, TcaConfiguration $configuration): TcaBuilderInterface
    {
        $this->tableBuilder->configureInTab($tab, $configuration);
        return $this;
    }

    /**
     * @param string $position
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configureAtPosition(string $position, TcaConfiguration $configuration): TcaBuilderInterface
    {
        $this->tableBuilder->configureAtPosition($position, $configuration);
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function inheritConfigurationFromType(string $type): TcaBuilderInterface
    {
        $this->tableBuilder->inheritConfigurationFromType($type);
        return $this;
    }

    /**
     * @param string $tab
     * @param string $otherTab
     * @return $this
     */
    public function addOrMoveTabInFrontOfTab(string $tab, string $otherTab): TcaBuilderInterface
    {
        $this->tableBuilder->inheritConfigurationFromType($type);
        return $this;
    }
}