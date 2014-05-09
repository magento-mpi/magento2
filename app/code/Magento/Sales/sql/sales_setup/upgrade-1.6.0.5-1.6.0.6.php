<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;

$entitiesToAlter = array('quote_address', 'order_address');

$attributes = array(
    'vat_id' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT),
    'vat_is_valid' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT),
    'vat_request_id' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT),
    'vat_request_date' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT),
    'vat_request_success' => array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT)
);

foreach ($entitiesToAlter as $entityName) {
    foreach ($attributes as $attributeCode => $attributeParams) {
        $installer->addAttribute($entityName, $attributeCode, $attributeParams);
    }
}
