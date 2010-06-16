<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
$range = 100;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->addAttribute('catalog_category', 'layered_navigation_price_filter_range', array(
        'group'             => 'Display Settings',
        'type'              => 'int',
        'label'             => 'Layered Navigation Price Filter Range',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'default'           => $range,
        'sort_order'        => 60,
));
$attribute = $installer->getAttribute('catalog_category', 'layered_navigation_price_filter_range');

$installer->getConnection()
    ->query("
        INSERT IGNORE INTO {$installer->getTable('catalog_category_entity_int')} (entity_type_id, attribute_id, entity_id, value)
        SELECT {$attribute['entity_type_id']}, {$attribute['attribute_id']}, entity_id, {$range}
        FROM {$installer->getTable('catalog_category_entity')}
    ")
;


