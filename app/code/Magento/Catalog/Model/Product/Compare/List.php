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
class Magento_Catalog_Model_Product_Compare_List extends Magento_Object
{
    /**
     * Add product to Compare List
     *
     * @param int|Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    public function addProduct($product)
    {
        /* @var $item Magento_Catalog_Model_Product_Compare_Item */
        $item = Mage::getModel('Magento_Catalog_Model_Product_Compare_Item');
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
     * @return Magento_Catalog_Model_Product_Compare_List
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
        return Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Compare_Item_Collection');
    }

    /**
     * Remove product from compare list
     *
     * @param int|Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    public function removeProduct($product)
    {
        /* @var $item Magento_Catalog_Model_Product_Compare_Item */
        $item = Mage::getModel('Magento_Catalog_Model_Product_Compare_Item');
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
     * @param Magento_Catalog_Model_Product_Compare_Item $item
     * @return Magento_Catalog_Model_Product_Compare_List
     */
    protected function _addVisitorToItem($item)
    {
        $item->addVisitorId(Mage::getSingleton('Magento_Log_Model_Visitor')->getId());
        if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
            $item->addCustomerData(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer());
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
        return Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product_Compare_Item')
            ->getCount($customerId, $visitorId);
    }
}
