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
    public function hasAvailaleResources()
    {
        $user = Mage::getSingleton('Mage_Admin_Model_Session')->getUser();
        if ($user && $user->hasAvailableResources()) {
            return true;
        }
        return false;
    }
}
