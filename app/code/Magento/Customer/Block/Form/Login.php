<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Form;

/**
 * Customer login form block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Login extends \Magento\Framework\View\Element\Template
{
    /**
     * @var int
     */
    private $_username = -1;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * Checkout data
     *
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutData;

    /**
     * Core url
     *
     * @var \Magento\Core\Helper\Url
     */
    protected $coreUrl;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Checkout\Helper\Data $checkoutData
     * @param \Magento\Core\Helper\Url $coreUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Checkout\Helper\Data $checkoutData,
        \Magento\Core\Helper\Url $coreUrl,
        array $data = array()
    ) {
        $this->_customerHelper = $customerHelper;
        $this->_customerSession = $customerSession;
        $this->checkoutData = $checkoutData;
        $this->coreUrl = $coreUrl;

        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(__('Customer Login'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->_customerHelper->getLoginPostUrl();
    }

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        $url = $this->getData('create_account_url');
        if (is_null($url)) {
            $url = $this->_customerHelper->getRegisterUrl();
        }
        if ($this->checkoutData->isContextCheckout()) {
            $url = $this->coreUrl->addRequestParam($url, array('context' => 'checkout'));
        }
        return $url;
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_customerHelper->getForgotPasswordUrl();
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername(true);
        }
        return $this->_username;
    }
}
