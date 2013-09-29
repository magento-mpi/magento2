<?php
/**
 * Primary resource configuration. Only uses
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Resource_Primary implements Magento_Core_Model_Config_ResourceInterface
{
    protected $_resourceList;

    public function __construct(Magento_Core_Model_Config_Local $configLocal)
    {
        $this->_resourceList = $configLocal->getResources();
    }


    /**
     * Retrieve connection config
     *
     * @param string $resourceName
     * @return string
     */
    public function getConnectionName($resourceName)
    {
        return isset($this->_resourceList[$resourceName]['connection'])
            ? $this->_resourceList[$resourceName]['connection']
            : Magento_Core_Model_Config_Resource::DEFAULT_SETUP_CONNECTION;
    }
}