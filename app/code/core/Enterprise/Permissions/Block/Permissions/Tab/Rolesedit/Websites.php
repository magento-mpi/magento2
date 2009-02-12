<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Websites fieldset for admin roles edit tab
 *
 */
class Enterprise_Permissions_Block_Permissions_Tab_Rolesedit_Websites extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Admin_Model_Roles
     */
    protected $_role;

    /**
     * Initialize role object
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_role = Mage::getModel('admin/roles')->load(Mage::app()->getRequest()->getParam('rid'));
    }

    /**
     * Check whether role assumes all websites permissions
     *
     * @return bool
     */
    public function getIsEverythingAllowed()
    {
        if (!$this->_role->getId()) {
            return true;
        }
        return $this->_role->getIsAllPermissions();
    }

    /**
     * Get all available websites
     *
     * @return array
     */
    public function getWebsites()
    {
        return Mage::app()->getWebsites(true);
    }

    /**
     * Get the role object
     *
     * @return Mage_Admin_Model_Roles
     */
    public function getRole()
    {
        return $this->_role;
    }
}
