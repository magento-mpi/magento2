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
class Enterprise_Invitation_Block_Link extends Magento_Core_Block_Template
{
    /**
     * Invitation data
     *
     * @var Enterprise_Invitation_Helper_Data
     */
    protected $_invitationData = null;

    /**
     * @param Enterprise_Invitation_Helper_Data $invitationData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Invitation_Helper_Data $invitationData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_invitationData = $invitationData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Adding link to account links block link params if invitation
     * is allowed globally and for current website
     *
     * @return Enterprise_Invitation_Block_Link
     */
    public function addAccountLink()
    {
        if (Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabledOnFront()
            && Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()
        ) {
            /** @var $blockInstance Magento_Page_Block_Template_Links */
            $blockInstance = $this->getLayout()->getBlock('account.links');
            if ($blockInstance) {
                $blockInstance->addLink(
                    __('Send Invitations'),
                    $this->_invitationData->getCustomerInvitationFormUrl(),
                    __('Send Invitations'),
                    true,
                    array(),
                    1,
                    'id="invitation-send-link"'
                );
            }
        }
        return $this;
    }

    /**
     * Adding link to account links block link params if invitation
     * is allowed globally and for current website
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return Enterprise_Invitation_Block_Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if (Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabledOnFront()) {
            /** @var $blockInstance Magento_Customer_Block_Account_Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
