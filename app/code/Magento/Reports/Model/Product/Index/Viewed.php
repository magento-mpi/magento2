<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model\Product\Index;

/**
 * Catalog Viewed Product Index
 *
 * @method \Magento\Reports\Model\Resource\Product\Index\Viewed _getResource()
 * @method \Magento\Reports\Model\Resource\Product\Index\Viewed getResource()
 * @method \Magento\Reports\Model\Product\Index\Viewed setVisitorId(int $value)
 * @method \Magento\Reports\Model\Product\Index\Viewed setCustomerId(int $value)
 * @method int getProductId()
 * @method \Magento\Reports\Model\Product\Index\Viewed setProductId(int $value)
 * @method \Magento\Reports\Model\Product\Index\Viewed setStoreId(int $value)
 * @method string getAddedAt()
 * @method \Magento\Reports\Model\Product\Index\Viewed setAddedAt(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Viewed extends \Magento\Reports\Model\Product\Index\AbstractIndex
{
    /**
     * Cache key name for Count of product index
     *
     * @var string
     */
    protected $_countCacheKey = 'product_index_viewed_count';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Reports\Model\Resource\Product\Index\Viewed');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = array();

        if ($this->_registry->registry('current_product')) {
            $productIds[] = $this->_registry->registry('current_product')->getId();
        }

        return $productIds;
    }
}
