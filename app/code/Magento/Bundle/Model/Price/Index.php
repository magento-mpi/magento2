<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Price;

/**
 * Bundle Product Price Index
 *
 * @method \Magento\Bundle\Model\Resource\Price\Index getResource()
 * @method \Magento\Bundle\Model\Price\Index setEntityId(int $value)
 * @method int getWebsiteId()
 * @method \Magento\Bundle\Model\Price\Index setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method \Magento\Bundle\Model\Price\Index setCustomerGroupId(int $value)
 * @method float getMinPrice()
 * @method \Magento\Bundle\Model\Price\Index setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method \Magento\Bundle\Model\Price\Index setMaxPrice(float $value)
 */
class Index extends \Magento\Model\AbstractModel
{

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Bundle\Model\Resource\Price\Index');
    }

    /**
     * Reindex Bundle product Price Index
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Catalog\Model\Product\Condition\ConditionInterface|array|int $products
     * @return $this
     */
    public function reindex($products = null)
    {
        $this->_getResource()->reindex($products);
        return $this;
    }
}
