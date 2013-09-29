<?php
/**
 * Primary resource configuration. Only uses
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Resource;

class Primary implements \Magento\Core\Model\Config\ResourceInterface
{
    protected $_resourceList;

    public function __construct(\Magento\Core\Model\Config\Local $configLocal)
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
            : \Magento\Core\Model\Config\Resource::DEFAULT_SETUP_CONNECTION;
    }
}
