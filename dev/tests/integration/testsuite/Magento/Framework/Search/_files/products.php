<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Green socks')
    ->setSku('green_socks')
    ->setPrice(10)
    ->setWeight(1)
    ->setShortDescription("Unisex green socks for some good peoples")
    ->setTaxClassId(0)
    ->setTierPrice(
        array(
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ),
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ),
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ),
        )
    )
    ->setDescription('Unisex <b>green socks</b> for some good peoples')
    ->setMetaTitle('green socks metadata')
    ->setMetaKeyword('green,socks,unisex')
    ->setMetaDescription('green socks metadata description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setCategoryIds(array(2))
    ->setStockData(
        array(
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        )
    )
    ->setCanSaveCustomOptions(true)
    ->setProductOptions(
        array(
            array(
                'id' => 1,
                'option_id' => 0,
                'previous_group' => 'text',
                'title' => 'Stone',
                'type' => 'field',
                'is_require' => 1,
                'sort_order' => 0,
                'price' => 1,
                'price_type' => 'fixed',
                'sku' => 'stone-1',
                'max_characters' => 100
            )
        )
    )
    ->setHasOptions(true)
    ->save();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(2)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('White shorts')
    ->setSku('white_shorts')
    ->setPrice(12)
    ->setWeight(2)
    ->setShortDescription("Small white shorts for your children")
    ->setTaxClassId(0)
    ->setTierPrice(
        array(
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ),
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ),
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ),
        )
    )
    ->setDescription('Small <b>white shorts</b> for your children')
    ->setMetaTitle('white shorts for your children metadata')
    ->setMetaKeyword('white,shorts,children')
    ->setMetaDescription('white shorts for your children metadata description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setCategoryIds(array(2))
    ->setStockData(
        array(
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        )
    )
    ->setCanSaveCustomOptions(true)
    ->setProductOptions(
        array(
            array(
                'id' => 2,
                'option_id' => 0,
                'previous_group' => 'text',
                'title' => 'Gold',
                'type' => 'field',
                'is_require' => 1,
                'sort_order' => 0,
                'price' => 1,
                'price_type' => 'fixed',
                'sku' => 'Gold',
                'max_characters' => 100
            )
        )
    )
    ->setHasOptions(true)
    ->save();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Red trousers')
    ->setSku('red_trousers')
    ->setPrice(14)
    ->setWeight(3)
    ->setShortDescription("Red pants for men")
    ->setTaxClassId(0)
    ->setTierPrice(
        array(
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 8,
            ),
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::CUST_GROUP_ALL,
                'price_qty' => 5,
                'price' => 5,
            ),
            array(
                'website_id' => 0,
                'cust_group' => \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID,
                'price_qty' => 3,
                'price' => 5,
            ),
        )
    )
    ->setDescription('Red pants for <b>men</b>')
    ->setMetaTitle('Red trousers meta title')
    ->setMetaKeyword('red,trousers,meta,men')
    ->setMetaDescription('Red trousers meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setCategoryIds(array(2))
    ->setStockData(
        array(
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        )
    )
    ->setCanSaveCustomOptions(true)
    ->setProductOptions(
        array(
            array(
                'id' => 3,
                'option_id' => 0,
                'previous_group' => 'text',
                'title' => 'Silver',
                'type' => 'field',
                'is_require' => 1,
                'sort_order' => 0,
                'price' => 1,
                'price_type' => 'fixed',
                'sku' => 'silver',
                'max_characters' => 100
            )
        )
    )
    ->setHasOptions(true)
    ->save();

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Resource\Setup',
    array('resourceName' => 'catalog_setup')
);

/** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
$attribute = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Resource\Eav\Attribute'
);
$attribute->setData(
    array(
        'attribute_code' => 'test_configurable',
        'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
        'is_global' => 1,
        'is_user_defined' => 1,
        'frontend_input' => 'select',
        'is_unique' => 0,
        'is_required' => 1,
        'is_configurable' => 1,
        'is_searchable' => 0,
        'is_visible_in_advanced_search' => 0,
        'is_comparable' => 0,
        'is_filterable' => 0,
        'is_filterable_in_search' => 0,
        'is_used_for_promo_rules' => 0,
        'is_html_allowed_on_front' => 1,
        'is_visible_on_front' => 0,
        'used_in_product_listing' => 0,
        'used_for_sort_by' => 0,
        'frontend_label' => array('Test Configurable'),
        'backend_type' => 'int',
        'option' => array(
            'value' => array('option_0' => array('Option 1'), 'option_1' => array('Option 2')),
            'order' => array('option_0' => 1, 'option_1' => 2)
        )
    )
);
$attribute->save();


/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute->getId());

/** @var \Magento\Eav\Model\Config $eavConfig */
$eavConfig = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config');
$eavConfig->clear();

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\Resource\Setup',
    array('resourceName' => 'catalog_setup')
);

/* Create simple products per each option */
/** @var $options \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection */
$options = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection'
);
$options->setAttributeFilter($attribute->getId());

$attributeValues = array();
$productIds = array();
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$productIds = array(10, 20);
foreach ($options as $option) {
    /** @var $product \Magento\Catalog\Model\Product */
    $product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
    $productId = array_shift($productIds);
    $product->setTypeId(
        \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
    )->setId(
            $productId
        )->setAttributeSetId(
            $attributeSetId
        )->setWebsiteIds(
            array(1)
        )->setName(
            'Configurable Option' . $option->getId()
        )->setSku(
            'simple_' . $productId
        )->setPrice(
            10
        )->setTestConfigurable(
            $option->getId()
        )->setVisibility(
            \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE
        )->setStatus(
            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
        )->setStockData(
            array('use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1)
        )->save();

    $attributeValues[] = array(
        'label' => 'test',
        'attribute_id' => $attribute->getId(),
        'value_index' => $option->getId(),
        'is_percent' => false,
        'pricing_value' => 5
    );
    $productIds[] = $product->getId();
}

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
    ->setId(
        4
    )->setAttributeSetId(
        4
    )->setWebsiteIds(
        array(1)
    )->setName(
        'Configurable Product'
    )->setSku(
        'configurable'
    )->setPrice(
        100
    )->setVisibility(
        \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
    )->setStatus(
        \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
    )->setStockData(
        array('use_config_manage_stock' => 1, 'is_in_stock' => 1)
    )->setAssociatedProductIds(
        [1, 2]
    )->setConfigurableAttributesData(
        array(
            array(
                'attribute_id' => $attribute->getId(),
                'attribute_code' => $attribute->getAttributeCode(),
                'frontend_label' => 'test',
                'values' => $attributeValues
            )
        )
    )->setShortDescription("Configurable product for women")
    ->setDescription('Configurable <b>product</b> for women')
    ->save();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId('bundle')
    ->setId(
        5
    )->setAttributeSetId(
        4
    )->setWebsiteIds(
        array(1)
    )->setName(
        'Bundle Product'
    )->setSku(
        'bundle-product'
    )->setVisibility(
        \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
    )->setStatus(
        \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
    )->setStockData(
        ['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1]
    )->setBundleOptionsData(
        [
            [
                'title' => 'Bundle Product Items',
                'default_title' => 'Bundle Product Items',
                'type' => 'select', 'required' => 1,
                'delete' => ''
            ]
        ]
    )->setBundleSelectionsData(
        [[['product_id' => 1, 'selection_qty' => 1, 'selection_can_change_qty' => 1, 'delete' => '']]]
    )->save();
