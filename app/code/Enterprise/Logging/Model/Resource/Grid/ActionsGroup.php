<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Logging_Model_Resource_Grid_ActionsGroup implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Enterprise_Logging_Model_Config
     */
    protected $_config;

    /**
     * @param Enterprise_Logging_Model_Config $config
     */
    public function __construct(Enterprise_Logging_Model_Config $config)
    {
        $this->_config = $config;
    }

    public function toOptionArray()
    {
        return $this->_config->getLabels();
    }
}
