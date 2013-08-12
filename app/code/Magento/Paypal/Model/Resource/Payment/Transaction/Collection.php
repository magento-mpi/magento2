<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment transactions collection
 *
 * @deprecated since 1.6.2.0
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Model_Resource_Payment_Transaction_Collection
    extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Created Before filter
     *
     * @var string
     */
    protected $_createdBefore          = "";
    /**
     * Initialize collection items factory class
     */
    protected function _construct()
    {
        $this->_init('Magento_Paypal_Model_Payment_Transaction', 'Magento_Paypal_Model_Resource_Payment_Transaction');
        parent::_construct();
    }

    /**
     * CreatedAt filter setter
     *
     * @param string $date
     * @return Magento_Sales_Model_Resource_Order_Payment_Transaction_Collection
     */
    public function addCreatedBeforeFilter($date)
    {
        $this->_createdBefore = $date;
        return $this;
    }

    /**
     * Prepare filters
     *
     * @return Magento_Paypal_Model_Resource_Payment_Transaction_Collection
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        if ($this->isLoaded()) {
            return $this;
        }

        // filters
        if ($this->_createdBefore) {
            $this->getSelect()->where('main_table.created_at < ?', $this->_createdBefore);
        }
        return $this;
    }

    /**
     * Unserialize additional_information in each item
     *
     * @return Magento_Paypal_Model_Resource_Payment_Transaction_Collection
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }
        return parent::_afterLoad();
    }
}
