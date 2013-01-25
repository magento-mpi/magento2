<?php
/**
 * Application config loader interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Config_LoaderInterface
{
    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     * @param bool $useCache
     * @return mixed
     */
    public function load(Mage_Core_Model_Config_Base $config, $useCache = true);
}
