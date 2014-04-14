<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

/**
 * Install grouped product link type
 */
$data = array(
    'link_type_id' => \Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED,
    'code' => 'super'
);
$installer->getConnection()->insertOnDuplicate($installer->getTable('catalog_product_link_type'), $data);

/**
 * Install grouped product link attributes
 */
$select = $installer->getConnection()->select()->from(
    array('c' => $installer->getTable('catalog_product_link_attribute'))
)->where(
    "c.link_type_id=?",
    \Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED
);
$result = $installer->getConnection()->fetchAll($select);

if (!$result) {

    $data = array(
        array(
            'link_type_id' => \Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED,
            'product_link_attribute_code' => 'position',
            'data_type' => 'int'
        ),
        array(
            'link_type_id' => \Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED,
            'product_link_attribute_code' => 'qty',
            'data_type' => 'decimal'
        )
    );

    $installer->getConnection()->insertMultiple($installer->getTable('catalog_product_link_attribute'), $data);
}
