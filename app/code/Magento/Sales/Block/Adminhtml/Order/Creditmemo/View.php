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
 * Adminhtml creditmemo view
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Block\Adminhtml\Order\Creditmemo;

class View extends \Magento\Backend\Block\Widget\Form\Container
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

    /**
     * Add & remove control buttons
     *
     */
    protected function _construct()
    {
        $this->_objectId    = 'creditmemo_id';
        $this->_controller  = 'adminhtml_order_creditmemo';
        $this->_mode        = 'view';

        parent::_construct();

        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_removeButton('delete');

        if (!$this->getCreditmemo()) {
            return;
        }

        if ($this->getCreditmemo()->canCancel()) {
            $this->_addButton('cancel', array(
                'label'     => __('Cancel'),
                'class'     => 'delete',
                'onclick'   => 'setLocation(\''.$this->getCancelUrl().'\')'
                )
            );
        }

        if ($this->_isAllowedAction('Magento_Sales::emails')) {
            $this->addButton('send_notification', array(
                'label'     => __('Send Email'),
                'onclick'   => 'confirmSetLocation(\''
                . __('Are you sure you want to send a Credit memo email to customer?')
                . '\', \'' . $this->getEmailUrl() . '\')'
            ));
        }

        if ($this->getCreditmemo()->canRefund()) {
            $this->_addButton('refund', array(
                'label'     => __('Refund'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getRefundUrl().'\')'
                )
            );
        }

        if ($this->getCreditmemo()->canVoid()) {
            $this->_addButton('void', array(
                'label'     => __('Void'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getVoidUrl().'\')'
                )
            );
        }

        if ($this->getCreditmemo()->getId()) {
            $this->_addButton('print', array(
                'label'     => __('Print'),
                'class'     => 'save',
                'onclick'   => 'setLocation(\''.$this->getPrintUrl().'\')'
                )
            );
        }
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getCreditmemo()->getEmailSent()) {
            $emailSent = __('The credit memo email was sent');
        } else {
            $emailSent = __('the credit memo email is not sent');
        }
        return __('Credit Memo #%1 | %3 | %2 (%4)', $this->getCreditmemo()->getIncrementId(), $this->formatDate($this->getCreditmemo()->getCreatedAtDate(), 'medium', true), $this->getCreditmemo()->getStateName(), $emailSent);
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('sales/order/view', array(
            'order_id'  => $this->getCreditmemo() ? $this->getCreditmemo()->getOrderId() : null,
            'active_tab'=> 'order_creditmemos'
        ));
    }

    /**
     * Retrieve capture url
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->getUrl('sales/*/capture', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    /**
     * Retrieve void url
     *
     * @return string
     */
    public function getVoidUrl()
    {
        return $this->getUrl('sales/*/void', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    /**
     * Retrieve cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('sales/*/cancel', array('creditmemo_id'=>$this->getCreditmemo()->getId()));
    }

    /**
     * Retrieve email url
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl('sales/*/email', array(
            'creditmemo_id' => $this->getCreditmemo()->getId(),
            'order_id'      => $this->getCreditmemo()->getOrderId()
        ));
    }

    /**
     * Retrieve print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('sales/*/print', array(
            'creditmemo_id' => $this->getCreditmemo()->getId()
        ));
    }

    /**
     * Update 'back' button url
     *
     * @return \Magento\Backend\Block\Widget\Container|$this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getCreditmemo()->getBackUrl()) {
                return $this->_updateButton(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getCreditmemo()->getBackUrl() . '\')'
                );
            }

            return $this->_updateButton(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl('sales/creditmemo/') . '\')'
            );
        }
        return $this;
    }

    /**
     * Check whether action is allowed
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
