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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $session;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        $helper = $this->_helperFactory->get('Magento\Customer\Helper\Data');
        return $this->_customerSession->isLoggedIn() ? $helper->getLogoutUrl() : $helper->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_customerSession->isLoggedIn() ? __('Log Out') : __('Log In');
    }

}
