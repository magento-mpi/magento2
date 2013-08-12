<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_ImportExport_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('importexport_importdata'),
    'data',
    array(
        'type' => Magento_DB_Ddl_Table::TYPE_TEXT,
        'length' => '4G',
        'default' => '',
        'comment' => 'Data'
    )
);
