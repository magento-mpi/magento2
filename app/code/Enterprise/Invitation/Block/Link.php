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
            /** @var $blockInstance Mage_Customer_Block_Account_Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
