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
    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_checkoutData = null;

    /**
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($configCacheType, $coreData, $context, $data);
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
        return $this->_checkoutData->isAllowedGuestCheckout($this->getQuote());
    }
}
