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
class RegisterLink extends \Magento\View\Element\Html\Link
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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Data $customerHelper,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_customerHelper = $customerHelper;
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_customerHelper->getRegisterUrl();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
            return '';
        }
        return parent::_toHtml();
    }
}
