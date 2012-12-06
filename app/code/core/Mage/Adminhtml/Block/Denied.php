<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Denied extends Mage_Adminhtml_Block_Template
{
    public function hasAvailableResources()
    {
        $user = Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser();
        if ($user && $user->getHasAvailableResources()) {
            return true;
        }
        return false;
    }
}
