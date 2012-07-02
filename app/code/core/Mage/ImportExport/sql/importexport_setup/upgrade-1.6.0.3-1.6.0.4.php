<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_ImportExport_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('importexport_importdata'),
    'behavior',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 32,
        'nullable'  => false,
        'default' => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND,
        'comment' => 'Behavior',
    )
);
