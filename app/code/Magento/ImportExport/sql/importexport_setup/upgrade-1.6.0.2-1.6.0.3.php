<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('importexport_importdata'), 'entity_subtype',
    array(
        'TYPE'    => \Magento\DB\Ddl\Table::TYPE_TEXT,
        'LENGTH'  => 50,
        'COMMENT' => 'Defines entity subtype to have ability import entity data partially'
    )
);
