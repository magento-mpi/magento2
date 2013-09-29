<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Multishipping billing information
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Multishipping;

class Billing extends \Magento\Payment\Block\Form\Container
{
    /**
     * Prepare children blocks
     */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(
                __('Billing Information - %1', $headBlock->getDefaultTitle())
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod|null $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method && $method->canUseForMultishipping() && parent::_canUseMethod($method);
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    public function getSelectedMethodCode()
    {
        if ($method = $this->getQuote()->getPayment()->getMethod()) {
            return $method;
        }
        return false;
    }

    /**
     * Retrieve billing address
     *
     * @return \Magento\Sales\Model\Quote\Address
     */
    public function getAddress()
    {
        $address = $this->getData('address');
        if (is_null($address)) {
            $address = \Mage::getSingleton('Magento\Checkout\Model\Type\Multishipping')->getQuote()->getBillingAddress();
            $this->setData('address', $address);
        }
        return $address;
    }

    /**
     * Retrieve quote model object
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    /**
     * Getter
     *
     * @return float
     */
    public function getQuoteBaseGrandTotal()
    {
        return (float)$this->getQuote()->getBaseGrandTotal();
    }

    /**
     * Retrieve url for select billing address
     *
     * @return string
     */
    public function getSelectAddressUrl()
    {
        return $this->getUrl('*/multishipping_address/selectBilling');
    }

    /**
     * Retrieve data post destination url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        //return $this->getUrl('*/*/billingPost');
        return $this->getUrl('*/*/overview');
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/backtoshipping');
    }
}
