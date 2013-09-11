<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Bundle Product Price Index
 *
 * @method \Magento\Bundle\Model\Resource\Price\Index _getResource()
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
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Model\Price;

class Index extends \Magento\Core\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Bundle\Model\Resource\Price\Index');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return \Magento\Bundle\Model\Resource\Price\Index
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Reindex Product price
     *
     * @param int $productId
     * @param int $priceType
     * @return \Magento\Bundle\Model\Price\Index
     */
    protected function _reindexProduct($productId, $priceType)
    {
        $this->_getResource()->reindexProduct($productId, $priceType);
        return $this;
    }

    /**
     * Reindex Bundle product Price Index
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Catalog\Model\Product\Condition\ConditionInterface|array|int $products
     * @return \Magento\Bundle\Model\Price\Index
     */
    public function reindex($products = null)
    {
        $this->_getResource()->reindex($products);
        return $this;
    }

    /**
     * Add bundle price range index to Product collection
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return \Magento\Bundle\Model\Price\Index
     */
    public function addPriceIndexToCollection($collection)
    {
        $productObjects = array();
        $productIds     = array();
        foreach ($collection->getItems() as $product) {
            /* @var $product \Magento\Catalog\Model\Product */
            if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $productIds[] = $product->getEntityId();
                $productObjects[$product->getEntityId()] = $product;
            }
        }
        $websiteId  = \Mage::app()->getStore($collection->getStoreId())
            ->getWebsiteId();
        $groupId    = \Mage::getSingleton('Magento\Customer\Model\Session')
            ->getCustomerGroupId();

        $addOptionsToResult = false;
        $prices = $this->_getResource()->loadPriceIndex($productIds, $websiteId, $groupId);
        foreach ($productIds as $productId) {
            if (isset($prices[$productId])) {
                $productObjects[$productId]
                    ->setData('_price_index', true)
                    ->setData('_price_index_min_price', $prices[$productId]['min_price'])
                    ->setData('_price_index_max_price', $prices[$productId]['max_price']);
            }
            else {
                $addOptionsToResult = true;
            }
        }

        if ($addOptionsToResult) {
            $collection->addOptionsToResult();
        }

        return $this;
    }

    /**
     * Add price index to bundle product after load
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Bundle\Model\Price\Index
     */
    public function addPriceIndexToProduct($product)
    {
        $websiteId  = $product->getStore()->getWebsiteId();
        $groupId    = \Mage::getSingleton('Magento\Customer\Model\Session')
            ->getCustomerGroupId();
        $prices = $this->_getResource()
            ->loadPriceIndex($product->getId(), $websiteId, $groupId);
        if (isset($prices[$product->getId()])) {
            $product->setData('_price_index', true)
                ->setData('_price_index_min_price', $prices[$product->getId()]['min_price'])
                ->setData('_price_index_max_price', $prices[$product->getId()]['max_price']);
        }
        return $this;
    }
}
