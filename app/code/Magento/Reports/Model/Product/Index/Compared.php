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
 * @method \Magento\Reports\Model\Resource\Product\Index\Compared _getResource()
 * @method \Magento\Reports\Model\Resource\Product\Index\Compared getResource()
 * @method \Magento\Reports\Model\Product\Index\Compared setVisitorId(int $value)
 * @method \Magento\Reports\Model\Product\Index\Compared setCustomerId(int $value)
 * @method int getProductId()
 * @method \Magento\Reports\Model\Product\Index\Compared setProductId(int $value)
 * @method \Magento\Reports\Model\Product\Index\Compared setStoreId(int $value)
 * @method string getAddedAt()
 * @method \Magento\Reports\Model\Product\Index\Compared setAddedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Product\Index;

class Compared extends \Magento\Reports\Model\Product\Index\AbstractIndex
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
        $this->_init('\Magento\Reports\Model\Resource\Product\Index\Compared');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = array();

        /* @var $helper \Magento\Catalog\Helper\Product\Compare */
        $helper = \Mage::helper('Magento\Catalog\Helper\Product\Compare');

        if ($helper->hasItems()) {
            foreach ($helper->getItemCollection() as $_item) {
                $productIds[] = $_item->getEntityId();
            }
        }

        if (\Mage::registry('current_product')) {
            $productIds[] = \Mage::registry('current_product')->getId();
        }

        return array_unique($productIds);
    }
}
