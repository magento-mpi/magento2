<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer  Mage_Eav_Model_Entity_Setup*/
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

/** @var $groups Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection*/
$groups = Mage::getResourceModel('Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection');
foreach ($groups as $group) {
    /** @var $group Mage_Eav_Model_Entity_Attribute_Group*/
    $group->save();
}

$installer->endSetup();
