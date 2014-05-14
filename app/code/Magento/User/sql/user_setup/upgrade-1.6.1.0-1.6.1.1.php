<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;
$installer->startSetup();

// Add reset password link token column
$installer->getConnection()->dropTable($installer->getTable('admin_assert'));
$installer->getConnection()->dropColumn($installer->getTable('admin_rule'), 'assert_id');

$installer->endSetup();
