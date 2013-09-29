<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;
$groupPriceAttrId = $installer->getAttribute('catalog_product', 'group_price', 'attribute_id');
$priceAttrId = $installer->getAttribute('catalog_product', 'price', 'attribute_id');
$connection = $installer->getConnection();

// update sort_order of Group Price attribute to be after Price
$select = $connection->select()
    ->join(
        array('t2' => $installer->getTable('eav_entity_attribute')),
        't1.attribute_group_id = t2.attribute_group_id',
        array('sort_order' => new \Zend_Db_Expr('t2.sort_order + 1'))
    )->where('t1.attribute_id = ?', $groupPriceAttrId)
    ->where('t2.attribute_id = ?', $priceAttrId);
$query = $select->crossUpdateFromSelect(array('t1' => $installer->getTable('eav_entity_attribute')));
$connection->query($query);

// update sort_order of all other attributes to be after Group Price
$select = $connection->select()
    ->join(
        array('t2' => $installer->getTable('eav_entity_attribute')),
        't1.attribute_group_id = t2.attribute_group_id',
        array('sort_order' => new \Zend_Db_Expr('t1.sort_order + 1'))
    )->where('t1.attribute_id != ?', $groupPriceAttrId)
    ->where('t1.sort_order >= t2.sort_order')
    ->where('t2.attribute_id = ?', $groupPriceAttrId);
$query = $select->crossUpdateFromSelect(array('t1' => $installer->getTable('eav_entity_attribute')));
$connection->query($query);
