<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\ImportExport\Model\Resource\Setup */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('importexport_importdata'),
    'data',
    array(
        'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'length' => '4G',
        'default' => '',
        'comment' => 'Data'
    )
);
