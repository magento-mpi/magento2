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
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

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
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Core\Helper\PostData $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Core\Helper\PostData $postDataHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_customerHelper = $customerHelper;
        $this->_isScopePrivate = true;
        $this->_postDataHelper = $postDataHelper;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->isLoggedIn()
            ? $this->_customerHelper->getLogoutUrl()
            : $this->_customerHelper->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->isLoggedIn() ? __('Log Out') : __('Log In');
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

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH);
    }

}
