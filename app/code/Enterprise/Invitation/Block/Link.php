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
     * @return string
     */
    public function getHref()
    {
        return Mage::helper('Enterprise_Invitation_Helper_Data')->getCustomerInvitationFormUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabledOnFront()
            && Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn()
        ) {
            return parent::_toHtml();
        }
        return '';
    }
}
