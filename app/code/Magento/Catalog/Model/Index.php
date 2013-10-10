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
namespace Magento\Catalog\Model;

class Index
{
    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Model\Resource\Category
     */
    protected $_catalogCategory;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_catalogProduct;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param \Magento\Catalog\Model\Resource\Category $catalogCategory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        \Magento\Catalog\Model\Resource\Category $catalogCategory,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_catalogProduct = $catalogProduct;
        $this->_catalogCategory = $catalogCategory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Rebuild indexes
     *
     * @return \Magento\Catalog\Model\Index
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
