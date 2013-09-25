<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product price block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 */
class Magento_Adminhtml_Block_Catalog_Product_Price extends Magento_Catalog_Block_Product_Price
{
    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        parent::__construct($storeManager, $catalogData, $taxData, $coreData, $context, $registry, $data);
    }

    /**
     * @param null|string|bool|int|Magento_Core_Model_Store $storeId
     * @return bool|Magento_Core_Model_Website
     */
    public function getWebsite($storeId)
    {
        return $this->_storeManager->getStore($storeId)->getWebsite();
    }
}
