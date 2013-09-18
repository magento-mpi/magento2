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
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Status');
    }

    /**
     * Assign order status to particular state
     *
     * @param string $state
     * @param boolean $isDefault make the status as default one for state
     * @throws \Exception
     * @return \Magento\Sales\Model\Order\Status
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
     * @throws \Exception
     * @return \Magento\Sales\Model\Order\Status
     */
    public function unassignState($state)
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->_getResource()->unassignState($this->getStatus(), $state);
            $this->_getResource()->commit();
            $params = array('status' => $this->getStatus(), 'state' => $state);
            $this->_eventDispatcher->dispatch('sales_order_status_unassign', $params);
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
     * @param mixed $store
     * @return string
     */
    public function getStoreLabel($store = null)
    {
        $store = \Mage::app()->getStore($store);
        if (!$store->isAdmin()) {
            $labels = $this->getStoreLabels();
            if (isset($labels[$store->getId()])) {
                return $labels[$store->getId()];
            }
        }
        return __($this->getLabel());
    }

    /**
     * Load default status per state
     *
     * @param string $state
     * @return \Magento\Sales\Model\Order\Status
     */
    public function loadDefaultByState($state)
    {
        $this->load($state, 'default_state');
        return $this;
    }
}
