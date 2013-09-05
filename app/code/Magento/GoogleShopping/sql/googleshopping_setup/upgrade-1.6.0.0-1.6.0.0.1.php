<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->addColumn($installer->getTable('googleshopping_types'), 'category', array(
    'TYPE'    => \Magento\DB\Ddl\Table::TYPE_TEXT,
    'LENGTH'  => 40,
    'COMMENT' => 'Google product category',
));