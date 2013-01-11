<?php
/**
 * Base Application configuration loader (app/etc/*.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Local implements Mage_Core_Model_Config_LoaderInterface
{
    public function __construct(Mage_Core_Model_Dir $dirs)
    {

    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)  //$config is empty
    {
        // TODO: Implement load() method.
    }
}
