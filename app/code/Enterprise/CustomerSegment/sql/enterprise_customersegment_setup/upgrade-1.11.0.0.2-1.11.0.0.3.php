<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_CustomerSegment_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('enterprise_customersegment_segment'), 'apply_to', array(
    'type' => Magento_DB_Ddl_Table::TYPE_SMALLINT,
    'unsigned' => true,
    'nullable' => false,
    'default' => 0,
    'comment' => 'Customer types to which this segment applies'
));
