<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

/**
 * Customer register link
 */
class RegisterLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Data $customerHelper,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_customerHelper = $customerHelper;
        $this->_customerUrl = $customerUrl;
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->_customerHelper->isRegistrationAllowed()
            || $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
