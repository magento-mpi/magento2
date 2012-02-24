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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Rule data fixture
 */

$roleData = require 'roleData.php';
/** @var $role Mage_Api2_Model_Acl_Global_Role */
$role = Mage::getModel('api2/acl_global_role');
$role->setData($roleData['create']);
$role->save();

$permissions = Mage_Api2_Model_Acl_Global_Rule_Permission::toArray();
$roleId = $role->getId();
return array(
    'create' => array(
        'role_id'     => $roleId,
        'permission'  => Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW,
        'resource_id' => 'some/resource',
    ),
    'update' => array(
        'role_id'     => $roleId,
        'permission'  => Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_DENY,
        'resource_id' => 'someUpdate/resource',
    )
);
