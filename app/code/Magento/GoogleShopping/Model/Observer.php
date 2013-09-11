<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Shopping Observer
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model;

class Observer
{
    /**
     * Update product item in Google Content
     *
     * @param \Magento\Object $observer
     * @return \Magento\GoogleShopping\Model\Observer
     */
    public function saveProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            \Mage::getModel('\Magento\GoogleShopping\Model\MassOperations')
                ->synchronizeItems($items);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                ->addError('Cannot update Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Delete product item from Google Content
     *
     * @param \Magento\Object $observer
     * @return \Magento\GoogleShopping\Model\Observer
     */
    public function deleteProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            \Mage::getModel('\Magento\GoogleShopping\Model\MassOperations')
                ->deleteItems($items);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')
                ->addError('Cannot delete Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Get items which are available for update/delete when product is saved
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\GoogleShopping\Model\Resource\Item\Collection
     */
    protected function _getItemsCollection($product)
    {
        $items = \Mage::getResourceModel('\Magento\GoogleShopping\Model\Resource\Item\Collection')
            ->addProductFilterId($product->getId());
        if ($product->getStoreId()) {
            $items->addStoreFilter($product->getStoreId());
        }

        foreach ($items as $item) {
            if (!\Mage::getStoreConfigFlag('google/googleshopping/observed', $item->getStoreId())) {
                $items->removeItemByKey($item->getId());
            }
        }

        return $items;
    }

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  \Magento\Event\Observer $observer
     * @return \Magento\GoogleShopping\Model\Observer
     */
    public function checkSynchronizationOperations(\Magento\Event\Observer $observer)
    {
        $flag = \Mage::getSingleton('Magento\GoogleShopping\Model\Flag')->loadSelf();
        if ($flag->isExpired()) {
            \Mage::getModel('\Magento\AdminNotification\Model\Inbox')->addMajor(
                __('Google Shopping operation has expired.'),
                __('One or more google shopping synchronization operations failed because of timeout.')
            );
            $flag->unlock();
        }
        return $this;
    }
}
