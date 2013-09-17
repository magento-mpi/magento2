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
 * Adminhtml billing agreement view
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Block_Adminhtml_Billing_Agreement_View extends Magento_Backend_Block_Widget_Form_Container
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

    /**
     * Initialize view container
     *
     */
    protected function _construct()
    {
        $this->_objectId    = 'agreement';
        $this->_controller  = 'adminhtml_billing_agreement';
        $this->_mode        = 'view';
        $this->_blockGroup  = 'Magento_Sales';

        parent::_construct();

        if (!$this->_isAllowed('Magento_Sales::actions_manage')) {
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
        if ($agreement && $agreement->canCancel() && $this->_isAllowed('Magento_Sales::actions_manage')) {
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
        return $this->getUrl('*/*/cancel', array('agreement' => $this->_getBillingAgreement()->getAgreementId()));
    }

    /**
     * Retrieve billing agreement model
     *
     * @return Magento_Sales_Model_Billing_Agreement
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
