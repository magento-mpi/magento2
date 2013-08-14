<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer  Magento_Eav_Model_Entity_Setup*/
$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();
$tableName = $installer->getTable('eav_attribute_group');

$connection->addColumn($tableName, 'attribute_group_code', array(
    'type' => Magento_DB_Ddl_Table::TYPE_TEXT,
    'length' => '255',
    'comment' => 'Attribute Group Code',
));

$connection->addColumn($tableName, 'tab_group_code', array(
    'type' => Magento_DB_Ddl_Table::TYPE_TEXT,
    'length' => '255',
    'comment' => 'Tab Group Code',
));

/** @var $groups Magento_Eav_Model_Resource_Entity_Attribute_Group_Collection*/
$groups = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Group_Collection');
foreach ($groups as $group) {
    /** @var $group Magento_Eav_Model_Entity_Attribute_Group*/
    $group->save();
}

$installer->endSetup();
