<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup */
$installer = $this;

$installer->getConnection()->dropColumn($installer->getTable('magento_scheduled_operations'), 'entity_subtype');
