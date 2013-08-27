<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var Magento_Webapi_Model_Acl_Role $role */
$role = Mage::getModel('Magento_Webapi_Model_Acl_Role');
$role->setRoleName('test_role')->save();
