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
     * Catalog product compare
     *
     * @var Magento_Catalog_Helper_Product_Compare
     */
    protected $_productCompare = null;

    /**
     * @param Magento_Catalog_Helper_Product_Compare $productCompare
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Product_Compare $productCompare,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productCompare = $productCompare;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Reports\Model\Resource\Product\Index\Compared');
    }

    /**
     * Retrieve Exclude Product Ids List for Collection
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        $productIds = array();
        if ($this->_productCompare->hasItems()) {
            foreach ($this->_productCompare->getItemCollection() as $_item) {
                $productIds[] = $_item->getEntityId();
            }
        }

        if ($this->_coreRegistry->registry('current_product')) {
            $productIds[] = $this->_coreRegistry->registry('current_product')->getId();
        }

        return array_unique($productIds);
    }
}
