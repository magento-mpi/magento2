<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer  \Magento\Eav\Model\Entity\Setup*/
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();
$tableName = $installer->getTable('eav_attribute_group');

$connection->addColumn($tableName, 'attribute_group_code', array(
    'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
    'length' => '255',
    'comment' => 'Attribute Group Code',
));

$connection->addColumn($tableName, 'tab_group_code', array(
    'type' => \Magento\DB\Ddl\Table::TYPE_TEXT,
    'length' => '255',
    'comment' => 'Tab Group Code',
));

/** @var $groups \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection*/
$groups = $installer->getAttributeGroupCollectionFactory();
foreach ($groups as $group) {
    /** @var $group \Magento\Eav\Model\Entity\Attribute\Group*/
    $group->save();
}

$installer->endSetup();
