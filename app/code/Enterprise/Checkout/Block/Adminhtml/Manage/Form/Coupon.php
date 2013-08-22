<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout coupon code form
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Form_Coupon extends Magento_Adminhtml_Block_Template
{
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
        return Mage::registry('checkout_current_quote');
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
        if (!$this->_authorization->isAllowed('Enterprise_Checkout::update')) {
            return '';
        }
        return parent::_toHtml();
    }
}
