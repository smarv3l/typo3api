<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 02.07.17
 * Time: 22:13
 */

namespace Typo3Api\Builder;


use Typo3Api\Tca\TcaConfiguration;

interface TcaBuilderInterface
{
    /**
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configure(TcaConfiguration $configuration): TcaBuilderInterface;

    /**
     * @param string $tab
     * @param TcaConfiguration $configuration
     * @return $this
     */
    public function configureInTab(string $tab, TcaConfiguration $configuration): TcaBuilderInterface;

    /**
     * @param string $position
     * @param TcaConfiguration $configuration
     * @return mixed
     */
    public function configureAtPosition(string $position, TcaConfiguration $configuration): TcaBuilderInterface;

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