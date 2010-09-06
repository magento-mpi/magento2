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
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * User acl role
 *
 * @method Mage_Api_Model_Resource_Role _getResource()
 * @method Mage_Api_Model_Resource_Role getResource()
 * @method Mage_Api_Model_Acl_Role getParentId()
 * @method int setParentId(int $value)
 * @method Mage_Api_Model_Acl_Role getTreeLevel()
 * @method int setTreeLevel(int $value)
 * @method Mage_Api_Model_Acl_Role getSortOrder()
 * @method int setSortOrder(int $value)
 * @method Mage_Api_Model_Acl_Role getRoleType()
 * @method string setRoleType(string $value)
 * @method Mage_Api_Model_Acl_Role getUserId()
 * @method int setUserId(int $value)
 * @method Mage_Api_Model_Acl_Role getRoleName()
 * @method string setRoleName(string $value)
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Acl_Role extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('api/role');
    }
}
