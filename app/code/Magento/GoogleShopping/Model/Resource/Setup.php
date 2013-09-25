<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GoogleShopping_Model_Resource_Setup extends Magento_Core_Model_Resource_Setup
{
    /**
     * @var Magento_GoogleShopping_Helper_Data
     */
    protected $_googleShoppingData = null;

    /**
     * @param Magento_GoogleShopping_Helper_Data $googleShoppingData
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_GoogleShopping_Helper_Data $googleShoppingData,
        Magento_Core_Model_Resource_Setup_Context $context,
        $resourceName,
        $moduleName = 'Magento_GoogleShopping',
        $connectionName = ''
    ) {
        $this->_googleShoppingData = $googleShoppingData;
        parent::__construct($context, $resourceName, $moduleName, $connectionName);
    }

    /**
     * @return Magento_GoogleShopping_Helper_Data
     */
    public function getGoogleShoppingData()
    {
        return $this->_googleShoppingData;
    }
}
