<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create sidebar recently compared block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Pcompared extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_pcompared');
        $this->setDataId('pcompared');
    }

    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Recently Compared Products');
    }

    /**
     * Retrieve item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        $productCollection = $this->getData('item_collection');
        if (is_null($productCollection)) {
            // get products to skip
            $skipProducts = array();
            if ($collection = $this->getCreateOrderModel()->getCustomerCompareList()) {
                $collection = $collection->getItemCollection()
                    ->useProductItem(true)
                    ->setStoreId($this->getStoreId())
                    ->setCustomerId($this->getCustomerId())
                    ->load();
                foreach ($collection as $_item) {
                    $skipProducts[] = $_item->getProductId();
                }
            }

            // prepare products collection and apply visitors log to it
            $productCollection = Mage::getModel('Mage_Catalog_Model_Product')->getCollection()
                ->setStoreId($this->getQuote()->getStoreId())
                ->addStoreFilter($this->getQuote()->getStoreId())
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image');
            Mage::getResourceSingleton('Mage_Reports_Model_Resource_Event')->applyLogToCollection(
                $productCollection, Mage_Reports_Model_Event::EVENT_PRODUCT_COMPARE, $this->getCustomerId(), 0, $skipProducts
            );

            $productCollection->load();
            $this->setData('item_collection', $productCollection);
        }
        return $productCollection;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return bool
     */
    public function canRemoveItems()
    {
        return false;
    }

    /**
     * Get product Id
     *
     * @param Mage_Catalog_Model_Product $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier of block item
     *
     * @param   mixed $item
     * @return  int
     */
    public function getProductId($item) {
        return $item->getId();
    }
}
