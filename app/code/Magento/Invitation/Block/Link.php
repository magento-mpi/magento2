<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 *
 * @category   Magento
 * @package    Magento_Invitation
 */
namespace Magento\Invitation\Block;

class Link extends \Magento\Page\Block\Link
{
    /**
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationConfiguration;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Invitation\Helper\Data
     */
    protected $_invitationHelper;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Invitation\Helper\Data $invitationHelper
     * @param \Magento\Invitation\Model\Config $invitationConfiguration
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Invitation\Helper\Data $invitationHelper,
        \Magento\Invitation\Model\Config $invitationConfiguration,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_customerSession = $customerSession;
        $this->_invitationConfiguration = $invitationConfiguration;
        $this->_invitationHelper = $invitationHelper;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_invitationHelper->getCustomerInvitationFormUrl();
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->_invitationConfiguration->isEnabledOnFront()
            && $this->_customerSession->isLoggedIn()
        ) {
            return parent::_toHtml();
        }
        return '';
    }
}
