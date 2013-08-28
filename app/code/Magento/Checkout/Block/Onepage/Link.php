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
     * Checkout data
     *
     * @var Magento_Checkout_Helper_Data
     */
    protected $_checkoutData = null;

    /**
     * @param Magento_Checkout_Helper_Data $checkoutData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Helper_Data $checkoutData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($coreData, $context, $data);
    }

    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }

    public function isDisabled()
    {
        return !Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote()->validateMinimumAmount();
    }

    public function isPossibleOnepageCheckout()
    {
        return $this->_checkoutData->canOnepageCheckout();
    }
}
