<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Link extends Mage_Page_Block_Link
{
    /**
     * @var Enterprise_Invitation_Helper_Data
     */
    protected $_invitationConfiguration;
    /**
     * @var Mage_Customer_Model_Session
     */
    protected $_customerSession;
    /**
     * @var Enterprise_Invitation_Helper_Data
     */
    protected $_invitationHelper;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Customer_Model_Session $customerSession
     * @param Enterprise_Invitation_Helper_Data $invitationHelper
     * @param Enterprise_Invitation_Model_Config $invitationConfiguration
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Customer_Model_Session $customerSession,
        Enterprise_Invitation_Helper_Data $invitationHelper,
        Enterprise_Invitation_Model_Config $invitationConfiguration,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
     * Render block HTML
     *
     * @return string
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
