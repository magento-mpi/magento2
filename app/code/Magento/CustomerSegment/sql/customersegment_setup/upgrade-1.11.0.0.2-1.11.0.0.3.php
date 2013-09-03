<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_CustomerSegment_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('magento_customersegment_segment'), 'apply_to', array(
    'type' => \Magento\DB\Ddl\Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => 0,
    'comment' => 'Customer types to which this segment applies'
));
