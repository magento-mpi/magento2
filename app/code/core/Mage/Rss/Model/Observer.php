<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rss Observer Model
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Model_Observer
{

    /**
     * Clean cache for catalog review rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function reviewSaveAfter(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(Mage_Rss_Block_Catalog_Review::CACHE_TAG));

    }

    /**
     * Clean cache for notify stock rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderItemSaveAfterNotifyStock(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(Mage_Rss_Block_Catalog_NotifyStock::CACHE_TAG));

    }

    /**
     * Clean cache for catalog new orders rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderItemSaveAfterOrderNew(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(Mage_Rss_Block_Order_New::CACHE_TAG));

    }
}
