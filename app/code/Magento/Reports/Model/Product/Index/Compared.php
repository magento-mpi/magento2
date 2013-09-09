<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Compared Product Index Model
 *
 * @method Magento_Reports_Model_Resource_Product_Index_Compared _getResource()
 * @method Magento_Reports_Model_Resource_Product_Index_Compared getResource()
 * @method Magento_Reports_Model_Product_Index_Compared setVisitorId(int $value)
 * @method Magento_Reports_Model_Product_Index_Compared setCustomerId(int $value)
 * @method int getProductId()
 * @method Magento_Reports_Model_Product_Index_Compared setProductId(int $value)
 * @method Magento_Reports_Model_Product_Index_Compared setStoreId(int $value)
 * @method string getAddedAt()
 * @method Magento_Reports_Model_Product_Index_Compared setAddedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Product_Index_Compared extends Magento_Reports_Model_Product_Index_Abstract
{
    /**
     * Cache key name for Count of product index
     *
     * @var string
     */
    protected $_countCacheKey   = 'product_index_compared_count';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Reports_Model_Resource_Product_Index_Compared');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = array();

        /* @var $helper Magento_Catalog_Helper_Product_Compare */
        $helper = Mage::helper('Magento_Catalog_Helper_Product_Compare');

        if ($helper->hasItems()) {
            foreach ($helper->getItemCollection() as $_item) {
                $productIds[] = $_item->getEntityId();
            }
        }

        if ($this->_coreRegistry->registry('current_product')) {
            $productIds[] = $this->_coreRegistry->registry('current_product')->getId();
        }

        return array_unique($productIds);
    }
}
