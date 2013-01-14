<?php
/**
 * Primary application config (app/etc/*.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Primary extends Mage_Core_Model_Config_Base
{
    /**
     * @param Mage_Core_Model_Config_Loader_Primary $loader
     */
    public function __construct(Mage_Core_Model_Config_Loader_Primary $loader)
    {
        parent::__construct('<config/>');
        $loader->load($this);
    }
}
