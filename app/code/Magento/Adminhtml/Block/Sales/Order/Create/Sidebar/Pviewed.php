<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create sidebar recently view block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Sidebar_Pviewed extends Magento_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_pviewed');
        $this->setDataId('pviewed');
    }

    public function getHeaderText()
    {
        return __('Recently Viewed Products');
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
            $stores = array();
            $website = Mage::app()->getStore($this->getStoreId())->getWebsite();
            foreach ($website->getStores() as $store) {
                $stores[] = $store->getId();
            }

            $collection = Mage::getModel('Magento_Reports_Model_Event')
                ->getCollection()
                ->addStoreFilter($stores)
                ->addRecentlyFiler(Magento_Reports_Model_Event::EVENT_PRODUCT_VIEW, $this->getCustomerId(), 0);
            $productIds = array();
            foreach ($collection as $event) {
                $productIds[] = $event->getObjectId();
            }

            $productCollection = null;
            if ($productIds) {
                $productCollection = Mage::getModel('Magento_Catalog_Model_Product')
                    ->getCollection()
                    ->setStoreId($this->getQuote()->getStoreId())
                    ->addStoreFilter($this->getQuote()->getStoreId())
                    ->addAttributeToSelect('name')
                    ->addAttributeToSelect('price')
                    ->addAttributeToSelect('small_image')
                    ->addIdFilter($productIds)
                    ->load();
            }
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
     * Retrieve identifier of block item
     *
     * @param \Magento\Object $item
     * @return int
     */
    public function getIdentifierId($item)
    {
        return $item->getId();
    }
}
