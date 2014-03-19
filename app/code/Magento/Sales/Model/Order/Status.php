<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order;

class Status extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Status');
    }

    /**
     * Assign order status to particular state
     *
     * @param string $state
     * @param boolean $isDefault make the status as default one for state
     * @return $this
     * @throws \Exception
     */
    public function assignState($state, $isDefault = false)
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->assignState($this->getStatus(), $state, $isDefault);
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Unassigns order status from particular state
     *
     * @param string $state
     * @return $this
     * @throws \Exception
     */
    public function unassignState($state)
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->unassignState($this->getStatus(), $state);
            $this->_getResource()->commit();
            $params = array('status' => $this->getStatus(), 'state' => $state);
            $this->_eventManager->dispatch('sales_order_status_unassign', $params);
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Getter for status labels per store
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if ($this->hasData('store_labels')) {
            return $this->_getData('store_labels');
        }
        $labels = $this->_getResource()->getStoreLabels($this);
        $this->setData('store_labels', $labels);
        return $labels;
    }

    /**
     * Get status label by store
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getStoreLabel($store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $labels = $this->getStoreLabels();
        if (isset($labels[$store->getId()])) {
            return $labels[$store->getId()];
        } else {
            return __($this->getLabel());
        }
    }

    /**
     * Load default status per state
     *
     * @param string $state
     * @return $this
     */
    public function loadDefaultByState($state)
    {
        $this->load($state, 'default_state');
        return $this;
    }
}
