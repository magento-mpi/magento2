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
     * @var \Magento\Customer\Model\Registration
     */
    protected $_registration;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Registration $registration
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_registration = $registration;
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
        if (!$this->_registration->isAllowed()
            || $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
