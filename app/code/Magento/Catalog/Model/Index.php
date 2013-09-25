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
 * Catalog Category/Product Index
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Index
{
    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog category
     *
     * @var Magento_Catalog_Model_Resource_Category
     */
    protected $_catalogCategory;

    /**
     * Catalog product
     *
     * @var Magento_Catalog_Model_Resource_Product
     */
    protected $_catalogProduct;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Resource_Product $catalogProduct
     * @param Magento_Catalog_Model_Resource_Category $catalogCategory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Product $catalogProduct,
        Magento_Catalog_Model_Resource_Category $catalogCategory,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_catalogProduct = $catalogProduct;
        $this->_catalogCategory = $catalogCategory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Rebuild indexes
     *
     * @return Magento_Catalog_Model_Index
     */
    public function rebuild()
    {
        $this->_catalogCategory->refreshProductIndex();
        foreach ($this->_storeManager->getStores() as $store) {
            $this->_catalogProduct->refreshEnabledIndex($store);
        }
        return $this;
    }
}
