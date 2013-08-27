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
 * Adminhtml shipment create
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Shipment_Create extends Magento_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_shipment';
        $this->_mode = 'create';

        parent::_construct();

        //$this->_updateButton('save', 'label', __('Submit Shipment'));
        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    public function getHeaderText()
    {
        $header = __('New Shipment for Order #%1', $this->getShipment()->getOrder()->getRealOrderId());
        return $header;
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array('order_id' => $this->getShipment() ? $this->getShipment()->getOrderId() : null));
    }
}
