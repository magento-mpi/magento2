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
 * Flat sales order payment collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Address_Collection extends Magento_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_address_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_address_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Order_Address', 'Magento_Sales_Model_Resource_Order_Address');
    }

    /**
     * Redeclare after load method for dispatch event
     *
     * @return Magento_Sales_Model_Resource_Order_Address_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->_eventManager->dispatch($this->_eventPrefix . '_load_after', array(
            $this->_eventObject => $this
        ));

        return $this;
    }
}
