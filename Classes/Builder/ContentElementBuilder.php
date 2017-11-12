<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 02.07.17
 * Time: 22:12
 */

namespace Typo3Api\Builder;


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Typo3Api\Tca\ShowitemConfiguration;
use Typo3Api\Tca\TcaConfigurationInterface;

class ContentElementBuilder implements TcaBuilderInterface
{
    /**
     * @var TableBuilder
     */
    private $tableBuilder;

    /**
     * @var string
     */
    private $section;

    /**
     * @param string $cType
     * @param string $section
     * @return ContentElementBuilder
     */
    public static function create(string $extKey, string $cType, string $section = 'common'): ContentElementBuilder
    {
        return new static($extKey, $cType, $section);
    }

    public function __construct(string $extKey, string $cType, string $section = 'common')
    {
        $this->tableBuilder = new TableBuilder('tt_content', $cType);
        $this->section = $section;

        $icon = 'content-text';
        $humanName = ucfirst(trim(strtolower(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $cType))));

        // add new type to select choices
        // TODO allow to define a position in dropdown
        // TODO allow to define an icon
        $newSelectItem = [$humanName, $cType, $icon];
        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = $newSelectItem;

        // add new type to newContentElement wizard
        ExtensionManagementUtility::addPageTSConfig("
            mod.wizards.newContentElement.wizardItems.$this->section.elements.$cType {
                iconIdentifier = $icon
                title = $humanName
                description = $extKey : $cType
                tt_content_defValues {
                    CType = $cType
                }
            }
            mod.wizards.newContentElement.wizardItems.$this->section.show := addToList($cType)
        ");

        // add basic tt_content configuration
        $this->configureInTab(
            'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general',
            new ShowitemConfiguration([
                '--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general'
            ])
        );

        // TODO mark the following tabs as default tabs which currently isn't easily possible

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
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        if (strpos($title, "\n") !== false) {
            throw new \RuntimeException("A content element title must not contain newlines, got '$title'.");
        }

        $cType = $this->tableBuilder->getTypeName();

        foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as &$item) {
            if ($item[1] === $cType) {
                $item[0] = $title;
                break;
            }
        }

        ExtensionManagementUtility::addPageTSConfig("
            mod.wizards.newContentElement.wizardItems.$this->section.elements.$cType.title = $title
        ");

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $cType = $this->tableBuilder->getTypeName();

        foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as &$item) {
            if ($item[1] === $cType) {
                return $item[0];
            }
        }

        throw new \RuntimeException("Title of content element $cType was not found.");
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        if (strpos($description, "\n") !== false) {
            throw new \RuntimeException("A content element description must not contain newlines, got '$description'.");
        }

        $cType = $this->tableBuilder->getTypeName();

        ExtensionManagementUtility::addPageTSConfig("
            mod.wizards.newContentElement.wizardItems.$this->section.elements.$cType.description = $description
        ");

        return $this;
    }

    // i don't know any way to implement getDescription

    /**
     * @param TcaConfigurationInterface $configuration
     * @return $this
     */
    public function configure(TcaConfigurationInterface $configuration): TcaBuilderInterface
    {
        $this->tableBuilder->configure($configuration);
        return $this;
    }

    /**
     * @param string $tab
     * @param TcaConfigurationInterface $configuration
     * @return $this
     */
    public function configureInTab(string $tab, TcaConfigurationInterface $configuration): TcaBuilderInterface
    {
        $this->tableBuilder->configureInTab($tab, $configuration);
        return $this;
    }

    /**
     * @param string $position
     * @param TcaConfigurationInterface $configuration
     * @return $this
     */
    public function configureAtPosition(string $position, TcaConfigurationInterface $configuration): TcaBuilderInterface
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
        $this->tableBuilder->addOrMoveTabInFrontOfTab($tab, $otherTab);
        return $this;
    }
}