<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

// Add reset password link token column
$installer->getConnection()->dropTable($installer->getTable('admin_assert'));
$installer->getConnection()->dropColumn($installer->getTable('admin_rule'), 'assert_id');

$installer->endSetup();
