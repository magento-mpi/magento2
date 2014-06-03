<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Onepage;

/**
 * One page checkout status
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Payment extends \Magento\Checkout\Block\Onepage\AbstractOnepage
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->getCheckout()->setStepData(
            'payment',
            array('label' => __('Payment Information'), 'is_show' => $this->isShow())
        );
        parent::_construct();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getQuoteBaseGrandTotal()
    {
        return (double)$this->getQuote()->getBaseGrandTotal();
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $registerParam = $this->getRequest()->getParam('register');
        return array(
            'quoteBaseGrandTotal' => $this->getQuoteBaseGrandTotal(),
            'progressUrl' => $this->getUrl('checkout/onepage/progress'),
            'reviewUrl' => $this->getUrl('checkout/onepage/review'),
            'failureUrl' => $this->getUrl('checkout/cart'),
            'getAddressUrl' => $this->getUrl('checkout/onepage/getAddress') . 'address/',
            'checkout' => array(
                'suggestRegistration' => $registerParam || $registerParam === '',
                'saveUrl' => $this->getUrl('checkout/onepage/saveMethod')
            ),
            'billing' => array('saveUrl' => $this->getUrl('checkout/onepage/saveBilling')),
            'shipping' => array('saveUrl' => $this->getUrl('checkout/onepage/saveShipping')),
            'shippingMethod' => array('saveUrl' => $this->getUrl('checkout/onepage/saveShippingMethod')),
            'payment' => array(
                'defaultPaymentMethod' => $this->getChildBlock('methods')->getSelectedMethodCode(),
                'saveUrl' => $this->getUrl('checkout/onepage/savePayment')
            ),
            'review' => array(
                'saveUrl' => $this->getUrl('checkout/onepage/saveOrder'),
                'successUrl' => $this->getUrl('checkout/onepage/success')
            )
        );
    }
}
