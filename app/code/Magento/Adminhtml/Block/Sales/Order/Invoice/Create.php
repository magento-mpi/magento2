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
 * Adminhtml invoice create
 */
class Magento_Adminhtml_Block_Sales_Order_Invoice_Create extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_invoice';
        $this->_mode = 'create';

        parent::_construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Magento_Sales_Model_Invoice
     */
    public function getInvoice()
    {
        return $this->_coreRegistry->registry('current_invoice');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return ($this->getInvoice()->getOrder()->getForcedShipmentWithInvoice())
            ? __('New Invoice and Shipment for Order #%1', $this->getInvoice()->getOrder()->getRealOrderId())
            : __('New Invoice for Order #%1', $this->getInvoice()->getOrder()->getRealOrderId());
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array('order_id' => $this->getInvoice() ? $this->getInvoice()->getOrderId() : null)
        );
    }
}
