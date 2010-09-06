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
 * Enter description here ...
 *
 * @method Mage_Api_Model_Resource_Rules _getResource()
 * @method Mage_Api_Model_Resource_Rules getResource()
 * @method Mage_Api_Model_Rules getRoleId()
 * @method int setRoleId(int $value)
 * @method Mage_Api_Model_Rules getResourceId()
 * @method string setResourceId(string $value)
 * @method Mage_Api_Model_Rules getPrivileges()
 * @method string setPrivileges(string $value)
 * @method Mage_Api_Model_Rules getAssertId()
 * @method int setAssertId(int $value)
 * @method Mage_Api_Model_Rules getRoleType()
 * @method string setRoleType(string $value)
 * @method Mage_Api_Model_Rules getPermission()
 * @method string setPermission(string $value)
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Rules extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('api/rules');
    }

    public function update() {
        $this->getResource()->update($this);
        return $this;
    }

    public function getCollection() {
        return Mage::getResourceModel('api/permissions_collection');
    }

    public function saveRel() {
        $this->getResource()->saveRel($this);
        return $this;
    }
}
