<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat sales order status history collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Status_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Order_Status', 'Magento_Sales_Model_Resource_Order_Status');
    }

    /**
     * Get collection data as options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('status', 'label');
    }

    /**
     * Get collection data as options hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('status', 'label');
    }

    /**
     * Join order states table
     */
    public function joinStates()
    {
        if (!$this->getFlag('states_joined')) {
            $this->_idFieldName = 'status_state';
            $this->getSelect()->joinLeft(
                array('state_table' => $this->getTable('sales_order_status_state')),
                'main_table.status=state_table.status',
                array('state', 'is_default')
            );
            $this->setFlag('states_joined', true);
        }
        return $this;
    }

    /**
     * add state code filter to collection
     *
     * @param string $state
     */
    public function addStateFilter($state)
    {
        $this->joinStates();
        $this->getSelect()->where('state_table.state=?', $state);
        return $this;
    }

    /**
     * Define label order
     *
     * @param string $dir
     * @return Magento_Sales_Model_Resource_Order_Status_Collection
     */
    public function orderByLabel($dir = 'ASC')
    {
        $this->getSelect()->order('main_table.label '.$dir);
        return $this;
    }
}
