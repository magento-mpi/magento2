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
 * @method Magento_Bundle_Model_Resource_Price_Index _getResource()
 * @method Magento_Bundle_Model_Resource_Price_Index getResource()
 * @method Magento_Bundle_Model_Price_Index setEntityId(int $value)
 * @method int getWebsiteId()
 * @method Magento_Bundle_Model_Price_Index setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method Magento_Bundle_Model_Price_Index setCustomerGroupId(int $value)
 * @method float getMinPrice()
 * @method Magento_Bundle_Model_Price_Index setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method Magento_Bundle_Model_Price_Index setMaxPrice(float $value)
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Bundle_Model_Price_Index extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Bundle_Model_Resource_Price_Index');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Magento_Bundle_Model_Resource_Price_Index
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
     * @return Magento_Bundle_Model_Price_Index
     */
    protected function _reindexProduct($productId, $priceType)
    {
        $this->_getResource()->reindexProduct($productId, $priceType);
        return $this;
    }

    /**
     * Reindex Bundle product Price Index
     *
     * @param Magento_Catalog_Model_Product|Magento_Catalog_Model_Product_Condition_Interface|array|int $products
     * @return Magento_Bundle_Model_Price_Index
     */
    public function reindex($products = null)
    {
        $this->_getResource()->reindex($products);
        return $this;
    }

    /**
     * Add bundle price range index to Product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @return Magento_Bundle_Model_Price_Index
     */
    public function addPriceIndexToCollection($collection)
    {
        $productObjects = array();
        $productIds     = array();
        foreach ($collection->getItems() as $product) {
            /* @var $product Magento_Catalog_Model_Product */
            if ($product->getTypeId() == Magento_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $productIds[] = $product->getEntityId();
                $productObjects[$product->getEntityId()] = $product;
            }
        }
        $websiteId  = Mage::app()->getStore($collection->getStoreId())
            ->getWebsiteId();
        $groupId    = Mage::getSingleton('Magento_Customer_Model_Session')
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
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Bundle_Model_Price_Index
     */
    public function addPriceIndexToProduct($product)
    {
        $websiteId  = $product->getStore()->getWebsiteId();
        $groupId    = Mage::getSingleton('Magento_Customer_Model_Session')
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
