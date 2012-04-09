<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $entityType Mage_Eav_Model_Entity_Type */
$entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
$taxClasses = Mage::getResourceModel('tax/class_collection')->toArray();
$taxClass = reset($taxClasses['items']);

return array(
    'type_id' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'attribute_set_id' => $entityType->getDefaultAttributeSetId(),
    'sku' => 'simple' . uniqid(),
    'name' => 'Test',
    'description' => 'Test description',
    'short_description' => 'Test short description',
    'weight' => 125,
    'status' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
    'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
    'price' => 25.50,
    'tax_class_id' => $taxClass['class_id'],
    // Field should not be validated if "Use Config Settings" checkbox is set
    // thus invalid value should not raise error
    'stock_data' => array(
        'manage_stock' => 0,
        'use_config_manage_stock' => 0,
        'qty' => 100,
        'min_qty' => -10,
        'use_config_min_qty' => 0,
        'min_sale_qty' => 1,
        'max_sale_qty' => 10,
        'is_qty_decimal' => -1,
        'backorders' => -1,
        'use_config_backorders' => 0,
        'notify_stock_qty' => 'text',
        'use_config_notify_stock_qty' => 0,
        'enable_qty_increments' => -100,
        'use_config_enable_qty_increments' => 0,
        'is_in_stock' => 0
    )
);
