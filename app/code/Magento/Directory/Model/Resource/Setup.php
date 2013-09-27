<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Directory Resource Setup Model
 *
 * @category    Magento
 * @package     Magento_Directory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Directory_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_Directory_Helper_Data
     */
    protected $_directoryData;

    /**
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param Magento_Directory_Helper_Data $directoryData
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Core_Model_Resource_Setup_Context $context,
        Magento_Directory_Helper_Data $directoryData,
        $resourceName,
        $moduleName = 'Magento_Directory',
        $connectionName = ''
    ) {
        $this->_directoryData = $directoryData;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }


    /**
     * @return Magento_Directory_Helper_Data
     */
    public function getDirectoryData()
    {
        return $this->_directoryData;
    }
}
