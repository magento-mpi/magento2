<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget to display link to the category
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Catalog_Block_Category_Widget_Link
    extends Magento_Catalog_Block_Widget_Link
{
    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource_Url_Rewrite $urlRewrite
     * @param Magento_Catalog_Model_Resource_Category $resourceCategory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Resource_Url_Rewrite $urlRewrite,
        Magento_Catalog_Model_Resource_Category $resourceCategory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($urlRewrite, $storeManager, $coreData, $context, $data);
        $this->_entityResource = $resourceCategory;
    }
}
