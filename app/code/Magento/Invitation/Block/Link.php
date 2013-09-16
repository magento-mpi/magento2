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
class Magento_Invitation_Block_Link extends Magento_Page_Block_Link
{
    /**
     * @var Magento_Invitation_Helper_Data
     */
    protected $_invitationConfiguration;
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;
    /**
     * @var Magento_Invitation_Helper_Data
     */
    protected $_invitationHelper;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Invitation_Helper_Data $invitationHelper
     * @param Magento_Invitation_Model_Config $invitationConfiguration
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Invitation_Helper_Data $invitationHelper,
        Magento_Invitation_Model_Config $invitationConfiguration,
        Magento_Core_Helper_Data $coreData,
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
