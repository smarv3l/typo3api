<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 02.07.17
 * Time: 22:13
 */

namespace Nemo64\Typo3Api\Builder;


use Nemo64\Typo3Api\Tca\TcaConfigurationInterface;

interface TcaBuilderInterface
{
    /**
     * @param TcaConfigurationInterface $configuration
     * @return $this
     */
    public function configure(TcaConfigurationInterface $configuration): TcaBuilderInterface;

    /**
     * @param string $tab
     * @param TcaConfigurationInterface $configuration
     * @return $this
     */
    public function configureInTab(string $tab, TcaConfigurationInterface $configuration): TcaBuilderInterface;

    /**
     * @param string $position
     * @param TcaConfigurationInterface $configuration
     * @return $this
     */
    public function configureAtPosition(string $position, TcaConfigurationInterface $configuration): TcaBuilderInterface;

    /**
     * @param string $type
     * @return $this
     */
    public function inheritConfigurationFromType(string $type): TcaBuilderInterface;

    /**
     * @param string $tab
     * @param string $otherTab
     * @return $this
     */
    public function addOrMoveTabInFrontOfTab(string $tab, string $otherTab): TcaBuilderInterface;
}