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
class Magento_GoogleShopping_Model_Observer
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Admin session
     *
     * @var Magento_Core_Model_Session_Abstract
     */
    protected $_session;

    /**
     * Admin session
     *
     * @var Magento_GoogleShopping_Model_Flag
     */
    protected $_flag;

    /**
     * Mass operations factory
     *
     * @var Magento_GoogleShopping_Model_MassOperationsFactory
     */
    protected $_operationsFactory;

    /**
     * Inbox factory
     *
     * @var Magento_AdminNotification_Model_InboxFactory
     */
    protected $_inboxFactory;

    /**
     * Collection factory
     *
     * @var Magento_GoogleShopping_Model_Resource_Item_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_GoogleShopping_Model_Resource_Item_CollectionFactory $collectionFactory
     * @param Magento_GoogleShopping_Model_MassOperationsFactory $operationsFactory
     * @param Magento_AdminNotification_Model_InboxFactory $inboxFactory
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Session_Abstract $session
     * @param Magento_GoogleShopping_Model_Flag $flag
     */
    public function __construct(
        Magento_GoogleShopping_Model_Resource_Item_CollectionFactory $collectionFactory,
        Magento_GoogleShopping_Model_MassOperationsFactory $operationsFactory,
        Magento_AdminNotification_Model_InboxFactory $inboxFactory,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Session_Abstract $session,
        Magento_GoogleShopping_Model_Flag $flag
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_operationsFactory = $operationsFactory;
        $this->_inboxFactory = $inboxFactory;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_session = $session;
        $this->_flag = $flag;
    }

    /**
     * Update product item in Google Content
     *
     * @param Magento_Object $observer
     * @return Magento_GoogleShopping_Model_Observer
     */
    public function saveProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            $this->_operationsFactory->create()->synchronizeItems($items);
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_session->addError('Cannot update Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Delete product item from Google Content
     *
     * @param Magento_Object $observer
     * @return Magento_GoogleShopping_Model_Observer
     */
    public function deleteProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            $this->_operationsFactory->create()->deleteItems($items);
        } catch (Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->_session->addError('Cannot delete Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Get items which are available for update/delete when product is saved
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    protected function _getItemsCollection($product)
    {
        $items = $this->_collectionFactory->create()->addProductFilterId($product->getId());
        if ($product->getStoreId()) {
            $items->addStoreFilter($product->getStoreId());
        }

        foreach ($items as $item) {
            if (!$this->_coreStoreConfig->getConfigFlag('google/googleshopping/observed', $item->getStoreId())) {
                $items->removeItemByKey($item->getId());
            }
        }

        return $items;
    }

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  Magento_Event_Observer $observer
     * @return Magento_GoogleShopping_Model_Observer
     */
    public function checkSynchronizationOperations(Magento_Event_Observer $observer)
    {
        $flag = $this->_flag->loadSelf();
        if ($flag->isExpired()) {
            $this->_inboxFactory->create()->addMajor(
                __('Google Shopping operation has expired.'),
                __('One or more google shopping synchronization operations failed because of timeout.')
            );
            $flag->unlock();
        }
        return $this;
    }
}
