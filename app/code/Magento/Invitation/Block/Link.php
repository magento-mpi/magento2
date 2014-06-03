<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 *
 */
namespace Magento\Invitation\Block;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationConfiguration;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Invitation\Helper\Data $invitationHelper
     * @param \Magento\Invitation\Model\Config $invitationConfiguration
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Invitation\Helper\Data $invitationHelper,
        \Magento\Invitation\Model\Config $invitationConfiguration,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_invitationConfiguration = $invitationConfiguration;
        $this->_invitationHelper = $invitationHelper;
        $this->_isScopePrivate = true;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_invitationHelper->getCustomerInvitationFormUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_invitationConfiguration->isEnabledOnFront()
            && $this->httpContext->getValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH)
        ) {
            return parent::_toHtml();
        }
        return '';
    }
}
