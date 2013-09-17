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
 */
class Magento_Adminhtml_Block_Sales_Order_Shipment_Create extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_shipment';
        $this->_mode = 'create';

        parent::_construct();

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
        return $this->_coreRegistry->registry('current_shipment');
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
            array('order_id' => $this->getShipment() ? $this->getShipment()->getOrderId() : null)
        );
    }
}
