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
class AuthorizationLink extends \Magento\Page\Block\Link
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
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Helper\Data $customerHelper,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->_customerSession = $session;
        $this->_customerHelper = $customerHelper;
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

}
