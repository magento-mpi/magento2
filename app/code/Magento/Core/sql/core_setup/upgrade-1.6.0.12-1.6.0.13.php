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

$installer->getConnection()->addColumn(
    $installer->getTable('core_theme'),
    'code',
    array(
        'type'    => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'comment' => 'Full theme code, including package'
    )
);

$installer->endSetup();
