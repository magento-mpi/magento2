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
 * Flat sales order grid collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Grid_Collection extends Magento_Sales_Model_Resource_Order_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_grid_collection';

    /**
     * Customer mode flag
     *
     * @var bool
     */
    protected $_customerModeFlag = false;

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales_flat_order_grid');
    }

    /**
     * Get SQL for get record count
     *
     * @return Magento_DB_Select
     */
    public function getSelectCountSql()
    {
        if ($this->getIsCustomerMode()) {
            $this->_renderFilters();

            $unionSelect = clone $this->getSelect();

            $unionSelect->reset(Zend_Db_Select::ORDER);
            $unionSelect->reset(Zend_Db_Select::LIMIT_COUNT);
            $unionSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

            $countSelect = clone $this->getSelect();
            $countSelect->reset();
            $countSelect->from(array('a' => $unionSelect), 'COUNT(*)');
        } else {
            $countSelect = parent::getSelectCountSql();
        }

        return $countSelect;
    }

    /**
     * Set customer mode flag value
     *
     * @param bool $value
     * @return Magento_Sales_Model_Resource_Order_Grid_Collection
     */
    public function setIsCustomerMode($value)
    {
        $this->_customerModeFlag = (bool)$value;
        return $this;
    }

    /**
     * Get customer mode flag value
     *
     * @return bool
     */
    public function getIsCustomerMode()
    {
        return $this->_customerModeFlag;
    }
}
