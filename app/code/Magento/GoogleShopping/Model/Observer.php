<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleShopping\Model;

/**
 * Google Shopping Observer
 */
class Observer
{
    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Admin session
     *
     * @var \Magento\GoogleShopping\Model\Flag
     */
    protected $_flag;

    /**
     * Mass operations factory
     *
     * @var \Magento\GoogleShopping\Model\MassOperationsFactory
     */
    protected $_operationsFactory;

    /**
     * Inbox factory
     *
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    protected $_inboxFactory;

    /**
     * Collection factory
     *
     * @var \Magento\GoogleShopping\Model\Resource\Item\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\GoogleShopping\Model\Resource\Item\CollectionFactory $collectionFactory
     * @param \Magento\GoogleShopping\Model\MassOperationsFactory $operationsFactory
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param \Magento\GoogleShopping\Model\Flag $flag
     */
    public function __construct(
        \Magento\GoogleShopping\Model\Resource\Item\CollectionFactory $collectionFactory,
        \Magento\GoogleShopping\Model\MassOperationsFactory $operationsFactory,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\Message\ManagerInterface $messageManager,
        \Magento\GoogleShopping\Model\Flag $flag
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_operationsFactory = $operationsFactory;
        $this->_inboxFactory = $inboxFactory;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->messageManager = $messageManager;
        $this->_flag = $flag;
    }

    /**
     * Update product item in Google Content
     *
     * @param \Magento\Object $observer
     * @return $this
     */
    public function saveProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            $this->_operationsFactory->create()->synchronizeItems($items);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->messageManager->addError('Cannot update Google Content Item. Google requires CAPTCHA.');
        }

        return $this;
    }

    /**
     * Delete product item from Google Content
     *
     * @param \Magento\Object $observer
     * @return $this
     */
    public function deleteProductItem($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $items = $this->_getItemsCollection($product);

        try {
            $this->_operationsFactory->create()->deleteItems($items);
        } catch (\Zend_Gdata_App_CaptchaRequiredException $e) {
            $this->messageManager->addError('Cannot delete Google Content Item. Google requires CAPTCHA.');
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
        $items = $this->_collectionFactory->create()->addProductFilterId($product->getId());
        if ($product->getStoreId()) {
            $items->addStoreFilter($product->getStoreId());
        }

        foreach ($items as $item) {
            if (!$this->_coreStoreConfig->isSetFlag('google/googleshopping/observed', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $item->getStoreId())) {
                $items->removeItemByKey($item->getId());
            }
        }

        return $items;
    }

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  \Magento\Event\Observer $observer
     * @return $this
     */
    public function checkSynchronizationOperations(\Magento\Event\Observer $observer)
    {
        $this->_flag->loadSelf();
        if ($this->_flag->isExpired()) {
            $this->_inboxFactory->create()->addMajor(
                __('Google Shopping operation has expired.'),
                __('One or more google shopping synchronization operations failed because of timeout.')
            );
            $this->_flag->unlock();
        }
        return $this;
    }
}
