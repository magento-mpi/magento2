<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('core_theme'),
    'code',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'comment' => 'Full theme code, including package')
);

$installer->endSetup();
