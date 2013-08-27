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
 * One page checkout status
 *
 * @category   Magento
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Onepage_Login extends Magento_Checkout_Block_Onepage_Abstract
{
    /**
     * Checkout data
     *
     * @var Magento_Checkout_Helper_Data
     */
    protected $_checkoutData = null;

    /**
     * @param Magento_Checkout_Helper_Data $checkoutData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Cache_Type_Config $configCacheType
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Helper_Data $checkoutData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($context, $configCacheType, $data);
    }

    protected function _construct()
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('login', array('label'=>__('Checkout Method'), 'allow'=>true));
        }
        parent::_construct();
    }

    public function getMessages()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getMessages(true);
    }

    public function getPostAction()
    {
        return Mage::getUrl('customer/account/loginPost', array('_secure'=>true));
    }

    public function getMethod()
    {
        return $this->getQuote()->getMethod();
    }

    public function getMethodData()
    {
        return $this->getCheckout()->getMethodData();
    }

    public function getSuccessUrl()
    {
        return $this->getUrl('*/*');
    }

    public function getErrorUrl()
    {
        return $this->getUrl('*/*');
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getUsername(true);
    }

    /**
     * Check if guests checkout is allowed
     *
     * @return bool
     */
    public function isAllowedGuestCheckout()
    {
        return $this->_checkoutData->isAllowedGuestCheckout($this->getQuote());
    }
}
