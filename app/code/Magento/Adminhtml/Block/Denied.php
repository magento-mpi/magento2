<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Denied extends Magento_Adminhtml_Block_Template
{
    public function hasAvailableResources()
    {
        $user = Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser();
        if ($user && $user->getHasAvailableResources()) {
            return true;
        }
        return false;
    }
}
