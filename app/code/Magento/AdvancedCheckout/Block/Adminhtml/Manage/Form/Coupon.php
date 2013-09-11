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
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Form;

class Coupon extends \Magento\Adminhtml\Block\Template
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
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return \Mage::registry('checkout_current_quote');
    }

    /**
     * Button html
     *
     * @return string
     */
    public function getApplyButtonHtml()
    {
        return $this->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Widget\Button')
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
