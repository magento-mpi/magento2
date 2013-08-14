<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Router_Config
{
    /**
     * @var Mage_Core_Model_Router_Config_Data
     */
    protected $_configData;

    /**
     * @param Mage_Core_Model_Router_Config_Data $configData
     */
    public function __construct(Mage_Core_Model_Router_Config_Data $configData)
    {
        $this->_configData = $configData;
    }

    /**
     * Get routers configuration
     *
     * @return array
     */
    public function getRouters()
    {
        $this->_configData->get('/routers');
    }
}
