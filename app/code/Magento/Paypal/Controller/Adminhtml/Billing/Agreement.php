<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Paypal\Controller\Adminhtml\Billing;

/**
 * Adminhtml billing agreement controller
 */
class Agreement extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Initialize billing agreement by ID specified in request
     *
     * @return \Magento\Paypal\Model\Billing\Agreement|false
     */
    protected function _initBillingAgreement()
    {
        $agreementId = $this->getRequest()->getParam('agreement');
        $agreementModel = $this->_objectManager->create('Magento\Paypal\Model\Billing\Agreement')->load($agreementId);

        if (!$agreementModel->getId()) {
            $this->messageManager->addError(__('Please specify the correct billing agreement ID and try again.'));
            return false;
        }

        $this->_coreRegistry->register('current_billing_agreement', $agreementModel);
        return $agreementModel;
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'index':
            case 'grid':
            case 'view':
                return $this->_authorization->isAllowed('Magento_Paypal::billing_agreement_actions_view');
            case 'cancel':
            case 'delete':
                return $this->_authorization->isAllowed('Magento_Paypal::actions_manage');
            default:
                return $this->_authorization->isAllowed('Magento_Paypal::billing_agreement');
        }
    }
}
