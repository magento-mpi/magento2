<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$configurablesCount = \Magento\ToolkitFramework\Config::getInstance()->getValue('configurable_products', 90);
$this->resetObjectManager();

/** @var \Magento\Core\Model\StoreManager $storeManager */
$storeManager = $this->getObjectManager()->create('\Magento\Core\Model\StoreManager');
/** @var $category \Magento\Catalog\Model\Category */
$category = $this->getObjectManager()->get('Magento\Catalog\Model\Category');

$result = array();
//Get all websites
$websites = $storeManager->getWebsites();
foreach($websites as $website) {
    $website_code = $website->getCode();
    //Get all groups
    $website_groups = $website->getGroups();
    foreach($website_groups as $website_group) {
        $website_group_root_category = $website_group->getRootCategoryId();
        $category->load($website_group_root_category);
        $categoryResource = $category->getResource();
        $root_category_name = $category->getName();
        //Get all categories
        $results_categories = $categoryResource->getAllChildren($category);
        foreach ($results_categories as $results_category) {
            $category->load($results_category);
            $structure = explode('/', $category->getPath());
            $pathSize  = count($structure);
            if ($pathSize > 1) {
                $path = array();
                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $category->load($structure[$i])->getName();
                }
                array_shift($path);
                $results_category_name = implode('/', $path);
            } else {
                $results_category_name = $category->getName();
            }
            //Deleted root categories
            if (trim($results_category_name)!='') {
                $result[$results_category] = array($website_code, $results_category_name, $root_category_name);
            }
        }
    }
}
$result = array_values($result);

$productWebsite = function ($index) use ($result) {
    return $result[$index % count($result)][0];
};
$productCategory = function ($index) use ($result) {
    return $result[$index % count($result)][1];
};
$productRootCategory = function ($index) use ($result) {
    return $result[$index % count($result)][2];
};

$headers = array (
   'sku',
   '_store',
   '_attribute_set',
   '_type',
   '_category',
   '_root_category',
   '_product_websites',
   'color',
   'configurable_variations',
   'cost',
   'country_of_manufacture',
   'created_at',
   'custom_design',
   'custom_design_from',
   'custom_design_to',
   'custom_layout_update',
   'description',
   'enable_googlecheckout',
   'gallery',
   'gift_message_available',
   'gift_wrapping_available',
   'gift_wrapping_price',
   'has_options',
   'image',
   'image_label',
   'is_returnable',
   'manufacturer',
   'meta_description',
   'meta_keyword',
   'meta_title',
   'minimal_price',
   'msrp',
   'msrp_display_actual_price_type',
   'msrp_enabled',
   'name',
   'news_from_date',
   'news_to_date',
   'options_container',
   'page_layout',
   'price',
   'quantity_and_stock_status',
   'related_tgtr_position_behavior',
   'related_tgtr_position_limit',
   'required_options',
   'short_description',
   'small_image',
   'small_image_label',
   'special_from_date',
   'special_price',
   'special_to_date',
   'status',
   'tax_class_id',
   'thumbnail',
   'thumbnail_label',
   'updated_at',
   'upsell_tgtr_position_behavior',
   'upsell_tgtr_position_limit',
   'url_key',
   'url_path',
   'variations',
   'variations_1382710717',
   'variations_1382710773',
   'variations_1382710861',
   'visibility',
   'weight',
   'qty',
   'min_qty',
   'use_config_min_qty',
   'is_qty_decimal',
   'backorders',
   'use_config_backorders',
   'min_sale_qty',
   'use_config_min_sale_qty',
   'max_sale_qty',
   'use_config_max_sale_qty',
   'is_in_stock',
   'notify_stock_qty',
   'use_config_notify_stock_qty',
   'manage_stock',
   'use_config_manage_stock',
   'use_config_qty_increments',
   'qty_increments',
   'use_config_enable_qty_inc',
   'enable_qty_increments',
   'is_decimal_divided',
   '_links_related_sku',
   '_links_related_position',
   '_links_crosssell_sku',
   '_links_crosssell_position',
   '_links_upsell_sku',
   '_links_upsell_position',
   '_associated_sku',
   '_associated_default_qty',
   '_associated_position',
   '_tier_price_website',
   '_tier_price_customer_group',
   '_tier_price_qty',
   '_tier_price_price',
   '_group_price_website',
   '_group_price_customer_group',
   '_group_price_price',
   '_media_attribute_id',
   '_media_image',
   '_media_label',
   '_media_position',
   '_media_is_disabled',
   '_super_products_sku',
   '_super_attribute_code',
   '_super_attribute_option',
   '_super_attribute_price_corr',
);

$rows = array (
    array (
        'sku' => 'Configurable Product %s-option 1',
        '_store' => '',
        '_attribute_set' => 'Default',
        '_type' => 'simple',
        '_category' => $productCategory,
        '_root_category' => $productRootCategory,
        '_product_websites' => $productWebsite,
        'color' => '',
        'configurable_variations' => 'option 1',
        'cost' => '',
        'country_of_manufacture' => '',
        'created_at' => '2013-10-25 15:12:32',
        'custom_design' => '',
        'custom_design_from' => '',
        'custom_design_to' => '',
        'custom_layout_update' => '',
        'description' => '<p>Configurable product description %s</p>',
        'enable_googlecheckout' => '1',
        'gallery' => '',
        'gift_message_available' => '',
        'gift_wrapping_available' => '',
        'gift_wrapping_price' => '',
        'has_options' => '0',
        'image' => '',
        'image_label' => '',
        'is_returnable' => 'Use config',
        'manufacturer' => '',
        'meta_description' => 'Configurable Product %s <p>Configurable product description 1</p>',
        'meta_keyword' => 'Configurable Product 1',
        'meta_title' => 'Configurable Product %s',
        'minimal_price' => '',
        'msrp' => '',
        'msrp_display_actual_price_type' => 'Use config',
        'msrp_enabled' => 'Use config',
        'name' => 'Configurable Product %s-option 1',
        'news_from_date' => '',
        'news_to_date' => '',
        'options_container' => 'Block after Info Column',
        'page_layout' => '',
        'price' => '10.0000',
        'quantity_and_stock_status' => 'In Stock',
        'related_tgtr_position_behavior' => '',
        'related_tgtr_position_limit' => '',
        'required_options' => '0',
        'short_description' => '',
        'small_image' => '',
        'small_image_label' => '',
        'special_from_date' => '',
        'special_price' => '',
        'special_to_date' => '',
        'status' => '1',
        'tax_class_id' => '2',
        'thumbnail' => '',
        'thumbnail_label' => '',
        'updated_at' => '2013-10-25 15:12:32',
        'upsell_tgtr_position_behavior' => '',
        'upsell_tgtr_position_limit' => '',
        'url_key' => 'configurable-product-%s-option-1',
        'url_path' => 'configurable-product-%s-option-1.html',
        'variations' => '',
        'variations_1382710717' => '',
        'variations_1382710773' => '',
        'variations_1382710861' => '',
        'visibility' => '1',
        'weight' => '1.0000',
        'qty' => '111.0000',
        'min_qty' => '0.0000',
        'use_config_min_qty' => '1',
        'is_qty_decimal' => '0',
        'backorders' => '0',
        'use_config_backorders' => '1',
        'min_sale_qty' => '1.0000',
        'use_config_min_sale_qty' => '1',
        'max_sale_qty' => '0.0000',
        'use_config_max_sale_qty' => '1',
        'is_in_stock' => '1',
        'notify_stock_qty' => '',
        'use_config_notify_stock_qty' => '1',
        'manage_stock' => '1',
        'use_config_manage_stock' => '1',
        'use_config_qty_increments' => '1',
        'qty_increments' => '0.0000',
        'use_config_enable_qty_inc' => '1',
        'enable_qty_increments' => '0',
        'is_decimal_divided' => '0',
        '_links_related_sku' => '',
        '_links_related_position' => '',
        '_links_crosssell_sku' => '',
        '_links_crosssell_position' => '',
        '_links_upsell_sku' => '',
        '_links_upsell_position' => '',
        '_associated_sku' => '',
        '_associated_default_qty' => '',
        '_associated_position' => '',
        '_tier_price_website' => '',
        '_tier_price_customer_group' => '',
        '_tier_price_qty' => '',
        '_tier_price_price' => '',
        '_group_price_website' => '',
        '_group_price_customer_group' => '',
        '_group_price_price' => '',
        '_media_attribute_id' => '',
        '_media_image' => '',
        '_media_label' => '',
        '_media_position' => '',
        '_media_is_disabled' => '',
        '_super_products_sku' => '',
        '_super_attribute_code' => '',
        '_super_attribute_option' => '',
        '_super_attribute_price_corr' => '',
    ),
    array (
        'sku' => 'Configurable Product %s-option 2',
        '_store' => '',
        '_attribute_set' => 'Default',
        '_type' => 'simple',
        '_category' => $productCategory,
        '_root_category' => $productRootCategory,
        '_product_websites' => $productWebsite,
        'color' => '',
        'configurable_variations' => 'option 2',
        'cost' => '',
        'country_of_manufacture' => '',
        'created_at' => '2013-10-25 15:12:35',
        'custom_design' => '',
        'custom_design_from' => '',
        'custom_design_to' => '',
        'custom_layout_update' => '',
        'description' => '<p>Configurable product description %s</p>',
        'enable_googlecheckout' => '1',
        'gallery' => '',
        'gift_message_available' => '',
        'gift_wrapping_available' => '',
        'gift_wrapping_price' => '',
        'has_options' => '0',
        'image' => '',
        'image_label' => '',
        'is_returnable' => 'Use config',
        'manufacturer' => '',
        'meta_description' => 'Configurable Product %s <p>Configurable product description 1</p>',
        'meta_keyword' => 'Configurable Product 1',
        'meta_title' => 'Configurable Product %s',
        'minimal_price' => '',
        'msrp' => '',
        'msrp_display_actual_price_type' => 'Use config',
        'msrp_enabled' => 'Use config',
        'name' => 'Configurable Product %s-option 2',
        'news_from_date' => '',
        'news_to_date' => '',
        'options_container' => 'Block after Info Column',
        'page_layout' => '',
        'price' => '10.0000',
        'quantity_and_stock_status' => 'In Stock',
        'related_tgtr_position_behavior' => '',
        'related_tgtr_position_limit' => '',
        'required_options' => '0',
        'short_description' => '',
        'small_image' => '',
        'small_image_label' => '',
        'special_from_date' => '',
        'special_price' => '',
        'special_to_date' => '',
        'status' => '1',
        'tax_class_id' => '2',
        'thumbnail' => '',
        'thumbnail_label' => '',
        'updated_at' => '2013-10-25 15:12:35',
        'upsell_tgtr_position_behavior' => '',
        'upsell_tgtr_position_limit' => '',
        'url_key' => 'configurable-product-%s-option-2',
        'url_path' => 'configurable-product-%s-option-2.html',
        'variations' => '',
        'variations_1382710717' => '',
        'variations_1382710773' => '',
        'variations_1382710861' => '',
        'visibility' => '1',
        'weight' => '1.0000',
        'qty' => '111.0000',
        'min_qty' => '0.0000',
        'use_config_min_qty' => '1',
        'is_qty_decimal' => '0',
        'backorders' => '0',
        'use_config_backorders' => '1',
        'min_sale_qty' => '1.0000',
        'use_config_min_sale_qty' => '1',
        'max_sale_qty' => '0.0000',
        'use_config_max_sale_qty' => '1',
        'is_in_stock' => '1',
        'notify_stock_qty' => '',
        'use_config_notify_stock_qty' => '1',
        'manage_stock' => '1',
        'use_config_manage_stock' => '1',
        'use_config_qty_increments' => '1',
        'qty_increments' => '0.0000',
        'use_config_enable_qty_inc' => '1',
        'enable_qty_increments' => '0',
        'is_decimal_divided' => '0',
        '_links_related_sku' => '',
        '_links_related_position' => '',
        '_links_crosssell_sku' => '',
        '_links_crosssell_position' => '',
        '_links_upsell_sku' => '',
        '_links_upsell_position' => '',
        '_associated_sku' => '',
        '_associated_default_qty' => '',
        '_associated_position' => '',
        '_tier_price_website' => '',
        '_tier_price_customer_group' => '',
        '_tier_price_qty' => '',
        '_tier_price_price' => '',
        '_group_price_website' => '',
        '_group_price_customer_group' => '',
        '_group_price_price' => '',
        '_media_attribute_id' => '',
        '_media_image' => '',
        '_media_label' => '',
        '_media_position' => '',
        '_media_is_disabled' => '',
        '_super_products_sku' => '',
        '_super_attribute_code' => '',
        '_super_attribute_option' => '',
        '_super_attribute_price_corr' => '',
    ),
    array (
        'sku' => 'Configurable Product %s-option 3',
        '_store' => '',
        '_attribute_set' => 'Default',
        '_type' => 'simple',
        '_category' => $productCategory,
        '_root_category' => $productRootCategory,
        '_product_websites' => $productWebsite,
        'color' => '',
        'configurable_variations' => 'option 3',
        'cost' => '',
        'country_of_manufacture' => '',
        'created_at' => '2013-10-25 15:12:37',
        'custom_design' => '',
        'custom_design_from' => '',
        'custom_design_to' => '',
        'custom_layout_update' => '',
        'description' => '<p>Configurable product description %s</p>',
        'enable_googlecheckout' => '1',
        'gallery' => '',
        'gift_message_available' => '',
        'gift_wrapping_available' => '',
        'gift_wrapping_price' => '',
        'has_options' => '0',
        'image' => '',
        'image_label' => '',
        'is_returnable' => 'Use config',
        'manufacturer' => '',
        'meta_description' => 'Configurable Product %s <p>Configurable product description 1</p>',
        'meta_keyword' => 'Configurable Product 1',
        'meta_title' => 'Configurable Product %s',
        'minimal_price' => '',
        'msrp' => '',
        'msrp_display_actual_price_type' => 'Use config',
        'msrp_enabled' => 'Use config',
        'name' => 'Configurable Product %s-option 3',
        'news_from_date' => '',
        'news_to_date' => '',
        'options_container' => 'Block after Info Column',
        'page_layout' => '',
        'price' => '10.0000',
        'quantity_and_stock_status' => 'In Stock',
        'related_tgtr_position_behavior' => '',
        'related_tgtr_position_limit' => '',
        'required_options' => '0',
        'short_description' => '',
        'small_image' => '',
        'small_image_label' => '',
        'special_from_date' => '',
        'special_price' => '',
        'special_to_date' => '',
        'status' => '1',
        'tax_class_id' => '2',
        'thumbnail' => '',
        'thumbnail_label' => '',
        'updated_at' => '2013-10-25 15:12:37',
        'upsell_tgtr_position_behavior' => '',
        'upsell_tgtr_position_limit' => '',
        'url_key' => 'configurable-product-%s-option-3',
        'url_path' => 'configurable-product-%s-option-3.html',
        'variations' => '',
        'variations_1382710717' => '',
        'variations_1382710773' => '',
        'variations_1382710861' => '',
        'visibility' => '1',
        'weight' => '1.0000',
        'qty' => '111.0000',
        'min_qty' => '0.0000',
        'use_config_min_qty' => '1',
        'is_qty_decimal' => '0',
        'backorders' => '0',
        'use_config_backorders' => '1',
        'min_sale_qty' => '1.0000',
        'use_config_min_sale_qty' => '1',
        'max_sale_qty' => '0.0000',
        'use_config_max_sale_qty' => '1',
        'is_in_stock' => '1',
        'notify_stock_qty' => '',
        'use_config_notify_stock_qty' => '1',
        'manage_stock' => '1',
        'use_config_manage_stock' => '1',
        'use_config_qty_increments' => '1',
        'qty_increments' => '0.0000',
        'use_config_enable_qty_inc' => '1',
        'enable_qty_increments' => '0',
        'is_decimal_divided' => '0',
        '_links_related_sku' => '',
        '_links_related_position' => '',
        '_links_crosssell_sku' => '',
        '_links_crosssell_position' => '',
        '_links_upsell_sku' => '',
        '_links_upsell_position' => '',
        '_associated_sku' => '',
        '_associated_default_qty' => '',
        '_associated_position' => '',
        '_tier_price_website' => '',
        '_tier_price_customer_group' => '',
        '_tier_price_qty' => '',
        '_tier_price_price' => '',
        '_group_price_website' => '',
        '_group_price_customer_group' => '',
        '_group_price_price' => '',
        '_media_attribute_id' => '',
        '_media_image' => '',
        '_media_label' => '',
        '_media_position' => '',
        '_media_is_disabled' => '',
        '_super_products_sku' => '',
        '_super_attribute_code' => '',
        '_super_attribute_option' => '',
        '_super_attribute_price_corr' => '',
    ),
    array (
        'sku' => 'Configurable Product %s',
        '_store' => '',
        '_attribute_set' => 'Default',
        '_type' => 'configurable',
        '_category' => $productCategory,
        '_root_category' => $productRootCategory,
        '_product_websites' => $productWebsite,
        'color' => '',
        'configurable_variations' => '',
        'cost' => '',
        'country_of_manufacture' => '',
        'created_at' => '2013-10-25 15:12:39',
        'custom_design' => '',
        'custom_design_from' => '',
        'custom_design_to' => '',
        'custom_layout_update' => '',
        'description' => '<p>Configurable product description %s</p>',
        'enable_googlecheckout' => '1',
        'gallery' => '',
        'gift_message_available' => '',
        'gift_wrapping_available' => '',
        'gift_wrapping_price' => '',
        'has_options' => '1',
        'image' => '',
        'image_label' => '',
        'is_returnable' => 'Use config',
        'manufacturer' => '',
        'meta_description' => 'Configurable Product %s <p>Configurable product description %s</p>',
        'meta_keyword' => 'Configurable Product %s',
        'meta_title' => 'Configurable Product %s',
        'minimal_price' => '',
        'msrp' => '',
        'msrp_display_actual_price_type' => 'Use config',
        'msrp_enabled' => 'Use config',
        'name' => 'Configurable Product %s',
        'news_from_date' => '',
        'news_to_date' => '',
        'options_container' => 'Block after Info Column',
        'page_layout' => '',
        'price' => '10.0000',
        'quantity_and_stock_status' => 'In Stock',
        'related_tgtr_position_behavior' => '',
        'related_tgtr_position_limit' => '',
        'required_options' => '1',
        'short_description' => '',
        'small_image' => '',
        'small_image_label' => '',
        'special_from_date' => '',
        'special_price' => '',
        'special_to_date' => '',
        'status' => '1',
        'tax_class_id' => '2',
        'thumbnail' => '',
        'thumbnail_label' => '',
        'updated_at' => '2013-10-25 15:12:39',
        'upsell_tgtr_position_behavior' => '',
        'upsell_tgtr_position_limit' => '',
        'url_key' => 'configurable-product-%s',
        'url_path' => 'configurable-product-%s.html',
        'variations' => '',
        'variations_1382710717' => '',
        'variations_1382710773' => '',
        'variations_1382710861' => '',
        'visibility' => '4',
        'weight' => '',
        'qty' => '0.0000',
        'min_qty' => '0.0000',
        'use_config_min_qty' => '1',
        'is_qty_decimal' => '0',
        'backorders' => '0',
        'use_config_backorders' => '1',
        'min_sale_qty' => '1.0000',
        'use_config_min_sale_qty' => '1',
        'max_sale_qty' => '0.0000',
        'use_config_max_sale_qty' => '1',
        'is_in_stock' => '1',
        'notify_stock_qty' => '',
        'use_config_notify_stock_qty' => '1',
        'manage_stock' => '1',
        'use_config_manage_stock' => '1',
        'use_config_qty_increments' => '1',
        'qty_increments' => '0.0000',
        'use_config_enable_qty_inc' => '1',
        'enable_qty_increments' => '0',
        'is_decimal_divided' => '0',
        '_links_related_sku' => '',
        '_links_related_position' => '',
        '_links_crosssell_sku' => '',
        '_links_crosssell_position' => '',
        '_links_upsell_sku' => '',
        '_links_upsell_position' => '',
        '_associated_sku' => '',
        '_associated_default_qty' => '',
        '_associated_position' => '',
        '_tier_price_website' => '',
        '_tier_price_customer_group' => '',
        '_tier_price_qty' => '',
        '_tier_price_price' => '',
        '_group_price_website' => '',
        '_group_price_customer_group' => '',
        '_group_price_price' => '',
        '_media_attribute_id' => '',
        '_media_image' => '',
        '_media_label' => '',
        '_media_position' => '',
        '_media_is_disabled' => '',
        '_super_products_sku' => 'Configurable Product %s-option 1',
        '_super_attribute_code' => 'configurable_variations',
        '_super_attribute_option' => 'option 1',
        '_super_attribute_price_corr' => '10.0000',
    ),
    array (
        'sku' => '',
        '_store' => '',
        '_attribute_set' => '',
        '_type' => '',
        '_category' => '',
        '_root_category' => '',
        '_product_websites' => '',
        'color' => '',
        'configurable_variations' => '',
        'cost' => '',
        'country_of_manufacture' => '',
        'created_at' => '',
        'custom_design' => '',
        'custom_design_from' => '',
        'custom_design_to' => '',
        'custom_layout_update' => '',
        'description' => '',
        'enable_googlecheckout' => '',
        'gallery' => '',
        'gift_message_available' => '',
        'gift_wrapping_available' => '',
        'gift_wrapping_price' => '',
        'has_options' => '',
        'image' => '',
        'image_label' => '',
        'is_returnable' => '',
        'manufacturer' => '',
        'meta_description' => '',
        'meta_keyword' => '',
        'meta_title' => '',
        'minimal_price' => '',
        'msrp' => '',
        'msrp_display_actual_price_type' => '',
        'msrp_enabled' => '',
        'name' => '',
        'news_from_date' => '',
        'news_to_date' => '',
        'options_container' => '',
        'page_layout' => '',
        'price' => '',
        'quantity_and_stock_status' => '',
        'related_tgtr_position_behavior' => '',
        'related_tgtr_position_limit' => '',
        'required_options' => '',
        'short_description' => '',
        'small_image' => '',
        'small_image_label' => '',
        'special_from_date' => '',
        'special_price' => '',
        'special_to_date' => '',
        'status' => '',
        'tax_class_id' => '',
        'thumbnail' => '',
        'thumbnail_label' => '',
        'updated_at' => '',
        'upsell_tgtr_position_behavior' => '',
        'upsell_tgtr_position_limit' => '',
        'url_key' => '',
        'url_path' => '',
        'variations' => '',
        'variations_1382710717' => '',
        'variations_1382710773' => '',
        'variations_1382710861' => '',
        'visibility' => '',
        'weight' => '',
        'qty' => '',
        'min_qty' => '',
        'use_config_min_qty' => '',
        'is_qty_decimal' => '',
        'backorders' => '',
        'use_config_backorders' => '',
        'min_sale_qty' => '',
        'use_config_min_sale_qty' => '',
        'max_sale_qty' => '',
        'use_config_max_sale_qty' => '',
        'is_in_stock' => '',
        'notify_stock_qty' => '',
        'use_config_notify_stock_qty' => '',
        'manage_stock' => '',
        'use_config_manage_stock' => '',
        'use_config_qty_increments' => '',
        'qty_increments' => '',
        'use_config_enable_qty_inc' => '',
        'enable_qty_increments' => '',
        'is_decimal_divided' => '',
        '_links_related_sku' => '',
        '_links_related_position' => '',
        '_links_crosssell_sku' => '',
        '_links_crosssell_position' => '',
        '_links_upsell_sku' => '',
        '_links_upsell_position' => '',
        '_associated_sku' => '',
        '_associated_default_qty' => '',
        '_associated_position' => '',
        '_tier_price_website' => '',
        '_tier_price_customer_group' => '',
        '_tier_price_qty' => '',
        '_tier_price_price' => '',
        '_group_price_website' => '',
        '_group_price_customer_group' => '',
        '_group_price_price' => '',
        '_media_attribute_id' => '',
        '_media_image' => '',
        '_media_label' => '',
        '_media_position' => '',
        '_media_is_disabled' => '',
        '_super_products_sku' => 'Configurable Product %s-option 2',
        '_super_attribute_code' => 'configurable_variations',
        '_super_attribute_option' => 'option 2',
        '_super_attribute_price_corr' => '20.0000',
    ),
    array (
        'sku' => '',
        '_store' => '',
        '_attribute_set' => '',
        '_type' => '',
        '_category' => '',
        '_root_category' => '',
        '_product_websites' => '',
        'color' => '',
        'configurable_variations' => '',
        'cost' => '',
        'country_of_manufacture' => '',
        'created_at' => '',
        'custom_design' => '',
        'custom_design_from' => '',
        'custom_design_to' => '',
        'custom_layout_update' => '',
        'description' => '',
        'enable_googlecheckout' => '',
        'gallery' => '',
        'gift_message_available' => '',
        'gift_wrapping_available' => '',
        'gift_wrapping_price' => '',
        'has_options' => '',
        'image' => '',
        'image_label' => '',
        'is_returnable' => '',
        'manufacturer' => '',
        'meta_description' => '',
        'meta_keyword' => '',
        'meta_title' => '',
        'minimal_price' => '',
        'msrp' => '',
        'msrp_display_actual_price_type' => '',
        'msrp_enabled' => '',
        'name' => '',
        'news_from_date' => '',
        'news_to_date' => '',
        'options_container' => '',
        'page_layout' => '',
        'price' => '',
        'quantity_and_stock_status' => '',
        'related_tgtr_position_behavior' => '',
        'related_tgtr_position_limit' => '',
        'required_options' => '',
        'short_description' => '',
        'small_image' => '',
        'small_image_label' => '',
        'special_from_date' => '',
        'special_price' => '',
        'special_to_date' => '',
        'status' => '',
        'tax_class_id' => '',
        'thumbnail' => '',
        'thumbnail_label' => '',
        'updated_at' => '',
        'upsell_tgtr_position_behavior' => '',
        'upsell_tgtr_position_limit' => '',
        'url_key' => '',
        'url_path' => '',
        'variations' => '',
        'variations_1382710717' => '',
        'variations_1382710773' => '',
        'variations_1382710861' => '',
        'visibility' => '',
        'weight' => '',
        'qty' => '',
        'min_qty' => '',
        'use_config_min_qty' => '',
        'is_qty_decimal' => '',
        'backorders' => '',
        'use_config_backorders' => '',
        'min_sale_qty' => '',
        'use_config_min_sale_qty' => '',
        'max_sale_qty' => '',
        'use_config_max_sale_qty' => '',
        'is_in_stock' => '',
        'notify_stock_qty' => '',
        'use_config_notify_stock_qty' => '',
        'manage_stock' => '',
        'use_config_manage_stock' => '',
        'use_config_qty_increments' => '',
        'qty_increments' => '',
        'use_config_enable_qty_inc' => '',
        'enable_qty_increments' => '',
        'is_decimal_divided' => '',
        '_links_related_sku' => '',
        '_links_related_position' => '',
        '_links_crosssell_sku' => '',
        '_links_crosssell_position' => '',
        '_links_upsell_sku' => '',
        '_links_upsell_position' => '',
        '_associated_sku' => '',
        '_associated_default_qty' => '',
        '_associated_position' => '',
        '_tier_price_website' => '',
        '_tier_price_customer_group' => '',
        '_tier_price_qty' => '',
        '_tier_price_price' => '',
        '_group_price_website' => '',
        '_group_price_customer_group' => '',
        '_group_price_price' => '',
        '_media_attribute_id' => '',
        '_media_image' => '',
        '_media_label' => '',
        '_media_position' => '',
        '_media_is_disabled' => '',
        '_super_products_sku' => 'Configurable Product %s-option 3',
        '_super_attribute_code' => 'configurable_variations',
        '_super_attribute_option' => 'option 3',
        '_super_attribute_price_corr' => '30.0000',
    ),
);

/**
 * Create configurable products
 */
$pattern = new \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Pattern();
$pattern->setHeaders($headers);
$pattern->setRowsSet($rows);

/** @var \Magento\ImportExport\Model\Import $import */
$import = $this->getObjectManager()->create(
    'Magento\ImportExport\Model\Import',
    array('data' => array('entity' => 'catalog_product', 'behavior' => 'append'))
);

$source = new \Magento\ToolkitFramework\ImportExport\Fixture\Complex\Generator($pattern, $configurablesCount);
// it is not obvious, but the validateSource() will actually save import queue data to DB
$import->validateSource($source);
// this converts import queue into actual entities
@$import->importSource();