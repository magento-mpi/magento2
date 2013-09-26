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
 * One page checkout cart link
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Link extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->_checkoutSession->getQuote()->validateMinimumAmount();
    }

    /**
     * @return bool
     */
    public function isPossibleOnepageCheckout()
    {
        return $this->helper('Magento_Checkout_Helper_Data')->canOnepageCheckout();
    }
}
