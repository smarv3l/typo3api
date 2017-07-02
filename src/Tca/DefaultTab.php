<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 02.07.17
 * Time: 13:36
 */

namespace Typo3Api\Tca;


interface DefaultTab extends TcaConfiguration
{
    /**
     * There are instances where fields should be seperated from the main fields.
     *
     * @return string
     */
    public function getDefaultTab(): string;
}