<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Index_Model_Observer
{
    /**
     * Indexer model
     *
     * @var Mage_Index_Model_Indexer
     */
    protected $_indexer;

    public function __construct()
    {
        $this->_indexer = Mage::getSingleton('Mage_Index_Model_Indexer');
    }

    /**
     * Store after commit observer. Process store related indexes
     *
     * @param Magento_Event_Observer $observer
     */
    public function processStoreSave(Magento_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $this->_indexer->processEntityAction(
            $store,
            Magento_Core_Model_Store::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

    /**
     * Store group after commit observer. Process store group related indexes
     *
     * @param Magento_Event_Observer $observer
     */
    public function processStoreGroupSave(Magento_Event_Observer $observer)
    {
        $storeGroup = $observer->getEvent()->getStoreGroup();
        $this->_indexer->processEntityAction(
            $storeGroup,
            Magento_Core_Model_Store_Group::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

    /**
     * Website save after commit observer. Process website related indexes
     *
     * @param Magento_Event_Observer $observer
     */
    public function processWebsiteSave(Magento_Event_Observer $observer)
    {
        $website = $observer->getEvent()->getWebsite();
        $this->_indexer->processEntityAction(
            $website,
            Magento_Core_Model_Website::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

    /**
     * Store after commit observer. Process store related indexes
     *
     * @param Magento_Event_Observer $observer
     */
    public function processStoreDelete(Magento_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $this->_indexer->processEntityAction(
            $store,
            Magento_Core_Model_Store::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
    }

    /**
     * Store group after commit observer. Process store group related indexes
     *
     * @param Magento_Event_Observer $observer
     */
    public function processStoreGroupDelete(Magento_Event_Observer $observer)
    {
        $storeGroup = $observer->getEvent()->getStoreGroup();
        $this->_indexer->processEntityAction(
            $storeGroup,
            Magento_Core_Model_Store_Group::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
    }

    /**
     * Website save after commit observer. Process website related indexes
     *
     * @param Magento_Event_Observer $observer
     */
    public function processWebsiteDelete(Magento_Event_Observer $observer)
    {
        $website = $observer->getEvent()->getWebsite();
        $this->_indexer->processEntityAction(
            $website,
            Magento_Core_Model_Website::ENTITY,
            Mage_Index_Model_Event::TYPE_DELETE
        );
    }

    /**
     * Config data after commit observer.
     *
     * @param Magento_Event_Observer $observer
     */
    public function processConfigDataSave(Magento_Event_Observer $observer)
    {
        $configData = $observer->getEvent()->getConfigData();
        $this->_indexer->processEntityAction(
            $configData,
            Magento_Core_Model_Config_Data::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE
        );
    }

}
