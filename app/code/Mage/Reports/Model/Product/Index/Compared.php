<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Compared Product Index Model
 *
 * @method Mage_Reports_Model_Resource_Product_Index_Compared _getResource()
 * @method Mage_Reports_Model_Resource_Product_Index_Compared getResource()
 * @method Mage_Reports_Model_Product_Index_Compared setVisitorId(int $value)
 * @method Mage_Reports_Model_Product_Index_Compared setCustomerId(int $value)
 * @method int getProductId()
 * @method Mage_Reports_Model_Product_Index_Compared setProductId(int $value)
 * @method Mage_Reports_Model_Product_Index_Compared setStoreId(int $value)
 * @method string getAddedAt()
 * @method Mage_Reports_Model_Product_Index_Compared setAddedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Product_Index_Compared extends Mage_Reports_Model_Product_Index_Abstract
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
        $this->_init('Mage_Reports_Model_Resource_Product_Index_Compared');
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

        if (Mage::registry('current_product')) {
            $productIds[] = Mage::registry('current_product')->getId();
        }

        return array_unique($productIds);
    }
}
