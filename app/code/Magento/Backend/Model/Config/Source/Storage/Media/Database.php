<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generate options for media database selection
 */
class Magento_Backend_Model_Config_Source_Storage_Media_Database implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Core_Model_ConfigInterface
     */
    protected $_config;

    /**
     * @param Magento_Core_Model_ConfigInterface $config
     */
    public function __construct(Magento_Core_Model_ConfigInterface $config)
    {
        $this->_config = $config;
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $connectionOptions = array();
        foreach (array_keys($this->_config->getConnections()) as $connectionName) {
            $connectionOptions[] = array('value' => $connectionName, 'label' => $connectionName);
        }
        sort($connectionOptions);
        reset($connectionOptions);
        return $connectionOptions;
    }
}
