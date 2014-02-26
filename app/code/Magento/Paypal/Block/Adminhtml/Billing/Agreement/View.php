<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement view
 */
namespace Magento\Paypal\Block\Adminhtml\Billing\Agreement;

class View extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize view container
     *
     */
    protected function _construct()
    {
        $this->_objectId    = 'agreement';
        $this->_controller  = 'adminhtml_billing_agreement';
        $this->_mode        = 'view';
        $this->_blockGroup  = 'Magento_Paypal';

        parent::_construct();

        if (!$this->_isAllowed('Magento_Paypal::actions_manage')) {
            $this->_removeButton('delete');
        }
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('billing_agreement_view');

        $this->_addButton('back', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);

        $agreement = $this->_getBillingAgreement();
        if ($agreement && $agreement->canCancel() && $this->_isAllowed('Magento_Paypal::actions_manage')) {
            $confirmText = __('Are you sure you want to do this?');
            $this->_addButton('cancel', array(
                'label'     => __('Cancel'),
                'onclick'   => "confirmSetLocation("
                    . "'{$confirmText}', '{$this->_getCancelUrl()}'"
                . ")",
                'class'     => 'cancel',
            ), -1);
        }
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Billing Agreement #%1', $this->_getBillingAgreement()->getReferenceId());
    }

    /**
     * Retrieve cancel billing agreement url
     *
     * @return string
     */
    protected function _getCancelUrl()
    {
        return $this->getUrl('adminhtml/*/cancel', array('agreement' => $this->_getBillingAgreement()->getAgreementId()));
    }

    /**
     * Retrieve billing agreement model
     *
     * @return \Magento\Paypal\Model\Billing\Agreement
     */
    protected function _getBillingAgreement()
    {
        return $this->_coreRegistry->registry('current_billing_agreement');
    }

    /**
     * Check current user permissions for specified action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowed($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
