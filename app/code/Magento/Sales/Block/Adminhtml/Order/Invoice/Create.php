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
 * Adminhtml invoice create
 */

namespace Magento\Sales\Block\Adminhtml\Order\Invoice;

class Create extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'adminhtml_order_invoice';
        $this->_mode = 'create';

        parent::_construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve invoice model instance
     *
     * @return \Magento\Sales\Model\Order\Invoice
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
            'sales/order/view',
            array('order_id' => $this->getInvoice() ? $this->getInvoice()->getOrderId() : null)
        );
    }
}
