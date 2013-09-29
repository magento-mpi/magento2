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
namespace Magento\Adminhtml\Helper\Dashboard;

class Order extends \Magento\Adminhtml\Helper\Dashboard\AbstractDashboard
{
    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManger;

    /**
     * @var \Magento\Reports\Model\Resource\Order\Collection
     */
    protected $_orderCollection;

    /**
     * @param \Magento\Reports\Model\Resource\Order\Collection $orderCollection
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Config $config
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Encryption $encryptor
     */
    public function __construct(
        \Magento\Reports\Model\Resource\Order\Collection $orderCollection,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Config $config,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Encryption $encryptor
    ) {
        $this->_orderCollection = $orderCollection;
        $this->_storeManger = $storeManager;
        parent::__construct($eventManager, $coreHttp, $context, $config, $coreStoreConfig, $encryptor);
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
                array('eq' => $this->_storeManger->getStore(\Magento\Core\Model\Store::ADMIN_CODE)->getId())
            );
        }



        $this->_collection->load();
    }

}
