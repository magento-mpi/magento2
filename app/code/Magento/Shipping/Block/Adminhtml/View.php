<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Block\Adminhtml;

/**
 * Adminhtml shipment create
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'shipment_id';
        $this->_mode = 'view';

        parent::_construct();

        $this->_removeButton('reset');
        $this->_removeButton('delete');
        if (!$this->getShipment()) {
            return;
        }

        if ($this->_authorization->isAllowed('Magento_Sales::emails')) {
            $this->_updateButton('save', 'label', __('Send Tracking Information'));
            $this->_updateButton(
                'save',
                'onclick',
                "deleteConfirm('" . __(
                    'Are you sure you want to send a Shipment email to customer?'
                ) . "', '" . $this->getEmailUrl() . "')"
            );
        }

        if ($this->getShipment()->getId()) {
            $this->_addButton(
                'print',
                array(
                    'label' => __('Print'),
                    'class' => 'save',
                    'onclick' => 'setLocation(\'' . $this->getPrintUrl() . '\')'
                )
            );
        }
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getShipment()->getEmailSent()) {
            $emailSent = __('the shipment email was sent');
        } else {
            $emailSent = __('the shipment email is not sent');
        }
        return __(
            'Shipment #%1 | %3 (%2)',
            $this->getShipment()->getIncrementId(),
            $emailSent,
            $this->formatDate($this->getShipment()->getCreatedAtDate(), 'medium', true)
        );
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'sales/order/view',
            array(
                'order_id' => $this->getShipment() ? $this->getShipment()->getOrderId() : null,
                'active_tab' => 'order_shipments'
            )
        );
    }

    /**
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('adminhtml/order_shipment/email', array('shipment_id' => $this->getShipment()->getId()));
    }

    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('sales/shipment/print', array('shipment_id' => $this->getShipment()->getId()));
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getShipment()->getBackUrl()) {
                return $this->_updateButton(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getShipment()->getBackUrl() . '\')'
                );
            }
            return $this->_updateButton(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl('sales/shipment/') . '\')'
            );
        }
        return $this;
    }
}
