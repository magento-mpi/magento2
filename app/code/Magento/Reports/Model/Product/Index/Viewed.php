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
 * Catalog Viewed Product Index
 *
 * @method Magento_Reports_Model_Resource_Product_Index_Viewed _getResource()
 * @method Magento_Reports_Model_Resource_Product_Index_Viewed getResource()
 * @method Magento_Reports_Model_Product_Index_Viewed setVisitorId(int $value)
 * @method Magento_Reports_Model_Product_Index_Viewed setCustomerId(int $value)
 * @method int getProductId()
 * @method Magento_Reports_Model_Product_Index_Viewed setProductId(int $value)
 * @method Magento_Reports_Model_Product_Index_Viewed setStoreId(int $value)
 * @method string getAddedAt()
 * @method Magento_Reports_Model_Product_Index_Viewed setAddedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Product_Index_Viewed extends Magento_Reports_Model_Product_Index_Abstract
{
    /**
     * Cache key name for Count of product index
     *
     * @var string
     */
    protected $_countCacheKey   = 'product_index_viewed_count';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Reports_Model_Resource_Product_Index_Viewed');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = array();

        if (Mage::registry('current_product')) {
            $productIds[] = Mage::registry('current_product')->getId();
        }

        return $productIds;
    }
}
