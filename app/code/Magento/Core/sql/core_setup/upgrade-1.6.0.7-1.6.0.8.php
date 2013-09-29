<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

/**
 * Add column 'updated_at' to 'core_layout_update'
 */
$connection->addColumn($installer->getTable('core_layout_update'), 'updated_at', array(
    'type'     => \Magento\DB\Ddl\Table::TYPE_TIMESTAMP,
    'nullable' => true,
    'comment'  => 'Last Update Timestamp'
));

$installer->endSetup();
