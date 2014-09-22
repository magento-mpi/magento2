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

$installer->getConnection()->dropTable($installer->getTable('admin_assert'));

$installer->endSetup();
