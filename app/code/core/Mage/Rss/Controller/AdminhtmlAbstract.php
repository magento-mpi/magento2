<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base class for Rss controllers, where admin login required for some actions
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Rss_Controller_AdminhtmlAbstract extends Mage_Core_Controller_Front_Action
{
    /**
     * Returns map of action to acl paths, needed to check user's access to a specific action
     *
     * @return array
     */
    abstract protected function _getAdminAclMap();

    /**
     * Controller predispatch method to change area for a specific action
     *
     * @return Mage_Rss_Controller_AdminhtmlAbstract
     */
    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        $map = $this->_getAdminAclMap();
        if (isset($map[$action])) {
            $this->setCurrentArea('adminhtml');
            $path = $map[$action];
            Mage::helper('Mage_Rss_Helper_Data')->authAdmin($path);
        }
        return parent::preDispatch();
    }
}
