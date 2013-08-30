<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Logging_Model_Resource_Grid_ActionsGroup implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Logging_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Logging_Model_Config $config
     */
    public function __construct(Magento_Logging_Model_Config $config)
    {
        $this->_config = $config;
    }

    public function toOptionArray()
    {
        return $this->_config->getLabels();
    }
}
