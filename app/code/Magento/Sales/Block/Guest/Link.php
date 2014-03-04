<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Guest;

/**
 * "Orders and Returns" link
 */
class Link extends \Magento\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\App\Http\Context $httpContext,
        array $data = array()
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->httpContext = $httpContext;
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)) {
            return '';
        }
        return parent::_toHtml();
    }
}
