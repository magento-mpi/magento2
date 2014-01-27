<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Account;

/**
 * Customer authorization link
 */
class AuthorizationLink extends \Magento\View\Element\Html\Link
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Magento\Core\Helper\PostData
     */
    protected $_postDataHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Core\Helper\PostData $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Core\Helper\PostData $postDataHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $session;
        $this->_customerHelper = $customerHelper;
        $this->_isScopePrivate = true;
        $this->_postDataHelper = $postDataHelper;
        if ($this->_customerSession->isLoggedIn()) {
            $this->_template = "Magento_Customer::account/link/logout.phtml";
        }
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_customerSession->isLoggedIn()
            ? $this->_customerHelper->getLogoutUrl()
            : $this->_customerHelper->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_customerSession->isLoggedIn() ? __('Log Out') : __('Log In');
    }

    /**
     * Retrieve params for post request
     *
     * @return string
     */
    public function getPostParams()
    {
        return $this->_postDataHelper->getPostData($this->getHref());
    }

}
