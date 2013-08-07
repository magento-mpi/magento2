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

$installer->getConnection()->addColumn(
    $installer->getTable('importexport_importdata'), 'entity_subtype',
    array(
        'TYPE'    => Magento_DB_Ddl_Table::TYPE_TEXT,
        'LENGTH'  => 50,
        'COMMENT' => 'Defines entity subtype to have ability import entity data partially'
    )
);
