<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml billing agreement view
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Billing_Agreement_View extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize view container
     *
     */
    public function __construct()
    {
        $this->_objectId    = 'agreement';
        $this->_controller  = 'adminhtml_billing_agreement';
        $this->_mode        = 'view';
        $this->_blockGroup  = 'Mage_Sales';

        parent::__construct();

        if (!$this->_isAllowed('sales/billing_agreement/actions/manage')) {
            $this->_removeButton('delete');
        }
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->setId('billing_agreement_view');

        $this->_addButton('back', array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() . '\')',
            'class'     => 'back',
        ), -1);

        $agreement = $this->_getBillingAgreement();
        if ($agreement && $agreement->canCancel() && $this->_isAllowed('sales/billing_agreement/actions/manage')) {
            $this->_addButton('cancel', array(
                'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Cancel'),
                'onclick'   => "confirmSetLocation('{$this->__('Are you sure you want to do this?')}', '{$this->_getCancelUrl()}')",
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
        return $this->__('Billing Agreement #%s', $this->_getBillingAgreement()->getReferenceId());
    }

    /**
     * Retrieve cancel billing agreement url
     *
     * @return string
     */
    protected function _getCancelUrl()
    {
        return $this->getUrl('*/*/cancel', array('agreement' => $this->_getBillingAgreement()->getAgreementId()));
    }

    /**
     * Retrieve billing agreement model
     *
     * @return Mage_Sales_Model_Billing_Agreement
     */
    protected function _getBillingAgreement()
    {
        return Mage::registry('current_billing_agreement');
    }

    /**
     * Check current user permissions for specified action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowed($action)
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed($action);
    }
}
