<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Model;

class Observer
{
    /**
     * Indexer model
     *
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    public function __construct()
    {
        $this->_indexer = \Mage::getSingleton('Magento\Index\Model\Indexer');
    }

    /**
     * Store after commit observer. Process store related indexes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processStoreSave(\Magento\Event\Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $this->_indexer->processEntityAction(
            $store,
            \Magento\Core\Model\Store::ENTITY,
            \Magento\Index\Model\Event::TYPE_SAVE
        );
    }

    /**
     * Store group after commit observer. Process store group related indexes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processStoreGroupSave(\Magento\Event\Observer $observer)
    {
        $storeGroup = $observer->getEvent()->getStoreGroup();
        $this->_indexer->processEntityAction(
            $storeGroup,
            \Magento\Core\Model\Store\Group::ENTITY,
            \Magento\Index\Model\Event::TYPE_SAVE
        );
    }

    /**
     * Website save after commit observer. Process website related indexes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processWebsiteSave(\Magento\Event\Observer $observer)
    {
        $website = $observer->getEvent()->getWebsite();
        $this->_indexer->processEntityAction(
            $website,
            \Magento\Core\Model\Website::ENTITY,
            \Magento\Index\Model\Event::TYPE_SAVE
        );
    }

    /**
     * Store after commit observer. Process store related indexes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processStoreDelete(\Magento\Event\Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $this->_indexer->processEntityAction(
            $store,
            \Magento\Core\Model\Store::ENTITY,
            \Magento\Index\Model\Event::TYPE_DELETE
        );
    }

    /**
     * Store group after commit observer. Process store group related indexes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processStoreGroupDelete(\Magento\Event\Observer $observer)
    {
        $storeGroup = $observer->getEvent()->getStoreGroup();
        $this->_indexer->processEntityAction(
            $storeGroup,
            \Magento\Core\Model\Store\Group::ENTITY,
            \Magento\Index\Model\Event::TYPE_DELETE
        );
    }

    /**
     * Website save after commit observer. Process website related indexes
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processWebsiteDelete(\Magento\Event\Observer $observer)
    {
        $website = $observer->getEvent()->getWebsite();
        $this->_indexer->processEntityAction(
            $website,
            \Magento\Core\Model\Website::ENTITY,
            \Magento\Index\Model\Event::TYPE_DELETE
        );
    }

    /**
     * Config data after commit observer.
     *
     * @param \Magento\Event\Observer $observer
     */
    public function processConfigDataSave(\Magento\Event\Observer $observer)
    {
        $configData = $observer->getEvent()->getConfigData();
        $this->_indexer->processEntityAction(
            $configData,
            \Magento\Core\Model\Config\Value::ENTITY,
            \Magento\Index\Model\Event::TYPE_SAVE
        );
    }

}
