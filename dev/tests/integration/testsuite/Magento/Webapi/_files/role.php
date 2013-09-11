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
/** @var \Magento\Webapi\Model\Acl\Role $role */
$role = Mage::getModel('\Magento\Webapi\Model\Acl\Role');
$role->setRoleName('test_role')->save();
