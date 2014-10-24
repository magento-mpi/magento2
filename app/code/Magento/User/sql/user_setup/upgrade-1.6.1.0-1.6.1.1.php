<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('admin_assert'));

$installer->endSetup();
