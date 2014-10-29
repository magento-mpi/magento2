<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('core_theme'),
    'code',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'comment' => 'Full theme code, including package')
);

$installer->endSetup();
