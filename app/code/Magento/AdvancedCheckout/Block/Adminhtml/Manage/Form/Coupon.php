<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout coupon code form
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Form_Coupon extends Magento_Adminhtml_Block_Template
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

    /**
     * Return applied coupon code for current quote
     *
     * @return string
     */
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }

    /**
     * Return current quote from regisrty
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('checkout_current_quote');
    }

    /**
     * Button html
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        return $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'id'        => 'apply_coupon',
                    'label'     => __('Apply'),
                    'onclick'   => "checkoutObj.applyCoupon($('coupon_code').value)",
                ))
            ->toHtml();
    }

    /**
     * Apply admin acl
     */
    protected function _toHtml()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')) {
            return '';
        }
        return parent::_toHtml();
    }
}
