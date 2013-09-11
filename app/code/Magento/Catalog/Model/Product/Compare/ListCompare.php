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
 * Product Compare List Model
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Compare;

class ListCompare extends \Magento\Object
{
    /**
     * Add product to Compare List
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Compare\ListCompare
     */
    public function addProduct($product)
    {
        /* @var $item \Magento\Catalog\Model\Product\Compare\Item */
        $item = \Mage::getModel('\Magento\Catalog\Model\Product\Compare\Item');
        $this->_addVisitorToItem($item);
        $item->loadByProduct($product);

        if (!$item->getId()) {
            $item->addProductData($product);
            $item->save();
        }

        return $this;
    }

    /**
     * Add products to compare list
     *
     * @param array $productIds
     * @return \Magento\Catalog\Model\Product\Compare\ListCompare
     */
    public function addProducts($productIds)
    {
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                $this->addProduct($productId);
            }
        }
        return $this;
    }

    /**
     * Retrieve Compare Items Collection
     *
     * @return product_compare_item_collection
     */
    public function getItemCollection()
    {
        return \Mage::getResourceModel('\Magento\Catalog\Model\Resource\Product\Compare\Item\Collection');
    }

    /**
     * Remove product from compare list
     *
     * @param int|\Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product\Compare\ListCompare
     */
    public function removeProduct($product)
    {
        /* @var $item \Magento\Catalog\Model\Product\Compare\Item */
        $item = \Mage::getModel('\Magento\Catalog\Model\Product\Compare\Item');
        $this->_addVisitorToItem($item);
        $item->loadByProduct($product);

        if ($item->getId()) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Add visitor and customer data to compare item
     *
     * @param \Magento\Catalog\Model\Product\Compare\Item $item
     * @return \Magento\Catalog\Model\Product\Compare\ListCompare
     */
    protected function _addVisitorToItem($item)
    {
        $item->addVisitorId(\Mage::getSingleton('Magento\Log\Model\Visitor')->getId());
        if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            $item->addCustomerData(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer());
        }

        return $this;
    }

    /**
     * Check has compare items by visitor/customer
     *
     * @param int $customerId
     * @param int $visitorId
     * @return bool
     */
    public function hasItems($customerId, $visitorId)
    {
        return \Mage::getResourceSingleton('\Magento\Catalog\Model\Resource\Product\Compare\Item')
            ->getCount($customerId, $visitorId);
    }
}
