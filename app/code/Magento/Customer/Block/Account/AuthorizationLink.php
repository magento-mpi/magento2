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
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
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
