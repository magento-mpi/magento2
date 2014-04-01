<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Helper\Dashboard;

/**
 * Adminhtml dashboard helper for orders
 */
class Order extends \Magento\Backend\Helper\Dashboard\AbstractDashboard
{
    /**
     * @var \Magento\Reports\Model\Resource\Order\Collection
     */
    protected $_orderCollection;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\State $appState
     * @param \Magento\Reports\Model\Resource\Order\Collection $orderCollection
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\App\State $appState,
        \Magento\Reports\Model\Resource\Order\Collection $orderCollection,
        $dbCompatibleMode = true
    ) {
        $this->_orderCollection = $orderCollection;
        parent::__construct($context, $coreStoreConfig, $storeManager, $appState, $dbCompatibleMode);
    }

    /**
     * @return void
     */
    protected function _initCollection()
    {
        $isFilter = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');

        $this->_collection = $this->_orderCollection->prepareSummary($this->getParam('period'), 0, 0, $isFilter);

        if ($this->getParam('store')) {
            $this->_collection->addFieldToFilter('store_id', $this->getParam('store'));
        } elseif ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', array('in' => implode(',', $storeIds)));
        } elseif ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $this->_collection->addFieldToFilter('store_id', array('in' => implode(',', $storeIds)));
        } elseif (!$this->_collection->isLive()) {
            $this->_collection->addFieldToFilter(
                'store_id',
                array('eq' => $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId())
            );
        }



        $this->_collection->load();
    }
}
