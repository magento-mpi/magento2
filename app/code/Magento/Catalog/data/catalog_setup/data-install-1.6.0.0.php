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

// Create Root Catalog Node
\Mage::getModel('Magento\Catalog\Model\Category')
    ->load(1)
    ->setId(1)
    ->setStoreId(0)
    ->setPath(1)
    ->setLevel(0)
    ->setPosition(0)
    ->setChildrenCount(0)
    ->setName('Root Catalog')
    ->setInitialSetupFlag(true)
    ->save();

/* @var $category \Magento\Catalog\Model\Category */
$category = \Mage::getModel('Magento\Catalog\Model\Category');

$category->setStoreId(0)
    ->setName('Default Category')
    ->setDisplayMode('PRODUCTS')
    ->setAttributeSetId($category->getDefaultAttributeSetId())
    ->setIsActive(1)
    ->setPath('1')
    ->setInitialSetupFlag(true)
    ->save();

$installer->setConfigData(\Magento\Catalog\Helper\Category::XML_PATH_CATEGORY_ROOT_ID, $category->getId());

$installer->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'Default', 'Design', 6);

$entityTypeId     = $installer->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

// update General Group
//$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name', 'General Information');
$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'sort_order', '10');

$groups = array(
    'display'   => array(
        'name'  => 'Display Settings',
        'sort'  => 20,
        'id'    => null
    ),
    'design'    => array(
        'name'  => 'Custom Design',
        'sort'  => 30,
        'id'    => null
    )
);

foreach ($groups as $k => $groupProp) {
    $installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupProp['name'], $groupProp['sort']);
    $groups[$k]['id'] = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupProp['name']);
}

// update attributes group and sort
$attributes = array(
    'custom_design'         => array(
        'group' => 'design',
        'sort'  => 10
    ),
//    'custom_design_apply'   => array(
//        'group' => 'design',
//        'sort'  => 20
//    ),
    'custom_design_from'    => array(
        'group' => 'design',
        'sort'  => 30
    ),
    'custom_design_to'      => array(
        'group' => 'design',
        'sort'  => 40
    ),
    'page_layout'           => array(
        'group' => 'design',
        'sort'  => 50
    ),
    'custom_layout_update'  => array(
        'group' => 'design',
        'sort'  => 60
    ),
    'display_mode'          => array(
        'group' => 'display',
        'sort'  => 10
    ),
    'landing_page'          => array(
        'group' => 'display',
        'sort'  => 20
    ),
    'is_anchor'             => array(
        'group' => 'display',
        'sort'  => 30
    ),
    'available_sort_by'     => array(
        'group' => 'display',
        'sort'  => 40
    ),
    'default_sort_by'       => array(
        'group' => 'display',
        'sort'  => 50
    ),
);

foreach ($attributes as $attributeCode => $attributeProp) {
    $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $groups[$attributeProp['group']]['id'],
        $attributeCode,
        $attributeProp['sort']
    );
}

/**
 * Install product link types
 */
$data = array(
    array(
        'link_type_id'  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
        'code'          => 'relation'
    ),
    array(
        'link_type_id'  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_GROUPED,
        'code'  => 'super'
    ),
    array(
        'link_type_id'  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL,
        'code'  => 'up_sell'
    ),
    array(
        'link_type_id'  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL,
        'code'  => 'cross_sell'
    ),
);

foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('catalog_product_link_type'), $bind);
}

/**
 * install product link attributes
 */
$data = array(
    array(
        'link_type_id'                  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_RELATED,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
    array(
        'link_type_id'                  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_GROUPED,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
    array(
        'link_type_id'                  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_GROUPED,
        'product_link_attribute_code'   => 'qty',
        'data_type'                     => 'decimal'
    ),
    array(
        'link_type_id'                  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_UPSELL,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
    array(
        'link_type_id'                  => \Magento\Catalog\Model\Product\Link::LINK_TYPE_CROSSSELL,
        'product_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
);

$installer->getConnection()->insertMultiple($installer->getTable('catalog_product_link_attribute'), $data);

/**
 * Remove Catalog specified attribute options (columns) from eav/attribute table
 *
 */
$describe = $installer->getConnection()->describeTable($installer->getTable('catalog_eav_attribute'));
foreach ($describe as $columnData) {
    if ($columnData['COLUMN_NAME'] == 'attribute_id') {
        continue;
    }
    $installer->getConnection()->dropColumn($installer->getTable('eav_attribute'), $columnData['COLUMN_NAME']);
}

