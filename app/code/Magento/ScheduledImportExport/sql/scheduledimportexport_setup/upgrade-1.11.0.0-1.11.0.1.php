<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->getConnection()
        ->modifyColumn($installer->getTable('magento_scheduled_operations'), 'force_import', array(
            'type'     => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
            'nullable' => false,
            'default'  => '0'
        ));
