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
namespace Magento\Adminhtml\Block\Sales\Order\Shipment;

class View extends \Magento\Adminhtml\Block\Widget\Form\Container
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
        $this->_objectId    = 'shipment_id';
        $this->_controller  = 'sales_order_shipment';
        $this->_mode        = 'view';

        parent::_construct();

        $this->_removeButton('reset');
        $this->_removeButton('delete');
        if (!$this->getShipment()) {
            return;
        }

        if ($this->_authorization->isAllowed('Magento_Sales::emails')) {
            $this->_updateButton('save', 'label', __('Send Tracking Information'));
            $this->_updateButton('save',
                'onclick', "deleteConfirm('"
                . __('Are you sure you want to send a Shipment email to customer?')
                . "', '" . $this->getEmailUrl() . "')"
            );
        }

        if ($this->getShipment()->getId()) {
            $this->_addButton('print', array(
                'label'     => __('Print'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
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

    public function getHeaderText()
    {
        if ($this->getShipment()->getEmailSent()) {
            $emailSent = __('the shipment email was sent');
        } else {
            $emailSent = __('the shipment email is not sent');
        }
        return __('Shipment #%1 | %3 (%2)', $this->getShipment()->getIncrementId(), $emailSent, $this->formatDate($this->getShipment()->getCreatedAtDate(), 'medium', true));
    }

    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array(
                'order_id' => $this->getShipment() ? $this->getShipment()->getOrderId() : null,
                'active_tab' => 'order_shipments'
        ));
    }

    public function getEmailUrl()
    {
        return $this->getUrl('*/sales_order_shipment/email', array('shipment_id'  => $this->getShipment()->getId()));
    }

    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array(
            'invoice_id' => $this->getShipment()->getId()
        ));
    }

    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getShipment()->getBackUrl()) {
                return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getShipment()->getBackUrl() . '\')');
            }
            return $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/sales_shipment/') . '\')');
        }
        return $this;
    }
}
