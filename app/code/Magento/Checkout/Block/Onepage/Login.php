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
namespace Magento\Checkout\Block\Onepage;

class Login extends \Magento\Checkout\Block\Onepage\AbstractOnepage
{
    protected function _construct()
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('login', array('label'=>__('Checkout Method'), 'allow'=>true));
        }
        parent::_construct();
    }

    public function getMessages()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getMessages(true);
    }

    public function getPostAction()
    {
        return \Mage::getUrl('customer/account/loginPost', array('_secure'=>true));
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
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getUsername(true);
    }

    /**
     * Check if guests checkout is allowed
     *
     * @return bool
     */
    public function isAllowedGuestCheckout()
    {
        return \Mage::helper('Magento\Checkout\Helper\Data')->isAllowedGuestCheckout($this->getQuote());
    }
}
