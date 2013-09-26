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
class Magento_Checkout_Block_Multishipping_Billing extends Magento_Payment_Block_Form_Container
{
    /**
     * @var Magento_Checkout_Model_Type_Multishipping
     */
    protected $_multishipping;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Type_Multishipping $multishipping
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Type_Multishipping $multishipping,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        $this->_multishipping = $multishipping;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare children blocks
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(
                __('Billing Information - %1', $headBlock->getDefaultTitle())
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Check payment method model
     *
     * @param Magento_Payment_Model_Method_Abstract|null $method
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
        $method = $this->getQuote()->getPayment()->getMethod();
        if ($method) {
            return $method;
        }
        return false;
    }

    /**
     * Retrieve billing address
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        $address = $this->getData('address');
        if (is_null($address)) {
            $address = $this->_multishipping->getQuote()->getBillingAddress();
            $this->setData('address', $address);
        }
        return $address;
    }

    /**
     * Retrieve quote model object
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
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
