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
class Magento_Invitation_Block_Link extends Magento_Core_Block_Template
{
    /**
     * Adding link to account links block link params if invitation
     * is allowed globally and for current website
     *
     * @return Magento_Invitation_Block_Link
     */
    public function addAccountLink()
    {
        if (Mage::getSingleton('Magento_Invitation_Model_Config')->isEnabledOnFront()
            && Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()
        ) {
            /** @var $blockInstance Magento_Page_Block_Template_Links */
            $blockInstance = $this->getLayout()->getBlock('account.links');
            if ($blockInstance) {
                $blockInstance->addLink(
                    __('Send Invitations'),
                    Mage::helper('Magento_Invitation_Helper_Data')->getCustomerInvitationFormUrl(),
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
     * @return Magento_Invitation_Block_Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if (Mage::getSingleton('Magento_Invitation_Model_Config')->isEnabledOnFront()) {
            /** @var $blockInstance Magento_Customer_Block_Account_Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
