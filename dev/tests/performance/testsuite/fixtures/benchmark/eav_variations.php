<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

define('ATTRIBUTE_SET_ID', 4);

/** @var \Magento\TestFramework\Application $this */

/* @var $model \Magento\Catalog\Model\Resource\Eav\Attribute */
$model = $this->getObjectManager()->create('Magento\Catalog\Model\Resource\Eav\Attribute');
/* @var $helper \Magento\Catalog\Helper\Product */
$helper = $this->getObjectManager()->get('Magento\Catalog\Helper\Product');

/**
 * Include default store view
 */
$storeViewsCount = \Magento\TestFramework\Helper\Cli::getOption('store_views', 4) + 1;

$data = [
    'frontend_label' => array_fill(0, $storeViewsCount + 1, 'configurable variations'),
    'frontend_input' => 'select',
    'is_required' => '0',
    'option' => [
        'order' => ['option_0' => '1', 'option_1' => '2', 'option_2' => '3'],
        'value' => [
            'option_0' => array_fill(0, $storeViewsCount + 1, 'option 1'),
            'option_1' => array_fill(0, $storeViewsCount + 1, 'option 2'),
            'option_2' => array_fill(0, $storeViewsCount + 1, 'option 3'),
        ],
        'delete' => ['option_0' => '', 'option_1' => '', 'option_2' => ''],
    ],
    'default' => ['option_0'],
    'attribute_code' => 'configurable_variations',
    'is_global' => '1',
    'default_value_text' => '',
    'default_value_yesno' => '0',
    'default_value_date' => '',
    'default_value_textarea' => '',
    'is_unique' => '0',
    'is_configurable' => '1',
    'is_searchable' => '0',
    'is_visible_in_advanced_search' => '0',
    'is_comparable' => '0',
    'is_filterable' => '0',
    'is_filterable_in_search' => '0',
    'is_used_for_promo_rules' => '0',
    'is_html_allowed_on_front' => '1',
    'is_visible_on_front' => '0',
    'used_in_product_listing' => '0',
    'used_for_sort_by' => '0',
    'source_model' => null,
    'backend_model' => null,
    'apply_to' => [],
    'backend_type' => 'int',
    'entity_type_id' => 4,
    'is_user_defined' => 1,
];
/**
 * The logic is not obvious, but looking to the controller logic for configurable products this attribute requires
 * to be saved twice to become a child of Default attribute set and become available for creating and|or importing
 * configurable products.
 * See MAGETWO-16492
 */
$model->addData($data);
$attributeSet = $this->getObjectManager()->get('Magento\Eav\Model\Entity\Attribute\Set');
$attributeSet->load(ATTRIBUTE_SET_ID);
$model->setAttributeSetId(ATTRIBUTE_SET_ID)->setAttributeGroupId($attributeSet->getDefaultGroupId(4))->save();

$model->setAttributeSetId(ATTRIBUTE_SET_ID);
$model->save();
