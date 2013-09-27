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
 * Adminhtml dashboard helper for orders
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Helper_Dashboard_Order extends Magento_Adminhtml_Helper_Dashboard_Abstract
{
    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManger;

    /**
     * @var Magento_Reports_Model_Resource_Order_Collection
     */
    protected $_orderCollection;

    /**
     * @param Magento_Reports_Model_Resource_Order_Collection $orderCollection
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Reports_Model_Resource_Order_Collection $orderCollection,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_orderCollection = $orderCollection;
        $this->_storeManger = $storeManager;
        parent::__construct($eventManager, $coreHttp, $context, $config, $coreStoreConfig);
    }

    protected function _initCollection()
    {
        $isFilter = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');

        $this->_collection = $this->_orderCollection->prepareSummary($this->getParam('period'), 0, 0, $isFilter);

        if ($this->getParam('store')) {
            $this->_collection->addFieldToFilter('store_id', $this->getParam('store'));
        } else if ($this->getParam('website')){
            $storeIds = $this->_storeManger->getWebsite($this->getParam('website'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', array('in' => implode(',', $storeIds)));
        } else if ($this->getParam('group')){
            $storeIds = $this->_storeManger->getGroup($this->getParam('group'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', array('in' => implode(',', $storeIds)));
        } elseif (!$this->_collection->isLive()) {
            $this->_collection->addFieldToFilter('store_id',
                array('eq' => $this->_storeManger->getStore(Magento_Core_Model_Store::ADMIN_CODE)->getId())
            );
        }



        $this->_collection->load();
    }

}
