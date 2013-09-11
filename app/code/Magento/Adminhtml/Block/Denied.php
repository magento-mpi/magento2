<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block;

class Denied extends \Magento\Adminhtml\Block\Template
{
    public function hasAvailableResources()
    {
        $user = \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser();
        if ($user && $user->getHasAvailableResources()) {
            return true;
        }
        return false;
    }
}
